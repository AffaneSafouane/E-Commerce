<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\CartService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartServiceTest extends TestCase
{
    public function testAddIncreasesQuantity(): void
    {
        $session = $this->createMock(SessionInterface::class);
        $session->expects($this->once())->method('get')->with('cart', [])->willReturn([42 => 1]);
        $session->expects($this->once())->method('set')->with('cart', [42 => 2]);

        $productRepository = $this->createMock(ProductRepository::class);
        // This assertion silences the Notice AND proves the repository is untouched
        $productRepository->expects($this->never())->method('find');

        $cartService = $this->createCartService($session, $productRepository);
        $cartService->add(42);
    }

    public function testDecreaseLowersQuantityWhenGreaterThanOne(): void
    {
        $session = $this->createMock(SessionInterface::class);
        $session->expects($this->once())->method('get')->with('cart', [])->willReturn([42 => 2]);
        $session->expects($this->once())->method('set')->with('cart', [42 => 1]);

        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->expects($this->never())->method('find');

        $cartService = $this->createCartService($session, $productRepository);
        $cartService->decrease(42);
    }

    public function testDecreaseRemovesItemWhenQuantityHitsZero(): void
    {
        $session = $this->createMock(SessionInterface::class);
        $session->expects($this->once())->method('get')->with('cart', [])->willReturn([42 => 1]);
        $session->expects($this->once())->method('set')->with('cart', []);

        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->expects($this->never())->method('find');

        $cartService = $this->createCartService($session, $productRepository);
        $cartService->decrease(42);
    }

    public function testGetTotalIncludesShippingFee(): void
    {
        $session = $this->createMock(SessionInterface::class);
        $session->expects($this->once())->method('get')->with('cart', [])->willReturn([10 => 2, 20 => 1]);
        $session->expects($this->never())->method('set');

        $firstProduct = (new Product())->setPriceHT(10.0);
        $secondProduct = (new Product())->setPriceHT(20.0);

        $productRepository = $this->createMock(ProductRepository::class);
        // Using willReturnCallback is much safer than willReturnMap for Doctrine methods
        $productRepository->expects($this->exactly(2))
            ->method('find')
            ->willReturnCallback(function (int $id) use ($firstProduct, $secondProduct) {
                if ($id === 10)
                    return $firstProduct;
                if ($id === 20)
                    return $secondProduct;
                return null;
            });

        $cartService = $this->createCartService($session, $productRepository);

        $this->assertSame(55.0, $cartService->getTotal());
    }

    /**
     * Helper to wire up the RequestStack boilerplate for every test
     */
    private function createCartService(SessionInterface $session, ProductRepository $productRepository): CartService
    {
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->expects($this->once())->method('getSession')->willReturn($session);

        return new CartService($requestStack, $productRepository);
    }

    public function testRemoveDeletesItemFromCart(): void
    {
        $session = $this->createMock(SessionInterface::class);
        $session->expects($this->once())->method('get')->with('cart', [])->willReturn([42 => 2, 10 => 1]);
        $session->expects($this->once())->method('set')->with('cart', [10 => 1]); // 42 is gone

        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->expects($this->never())->method('find');

        $cartService = $this->createCartService($session, $productRepository);
        $cartService->remove(42);
    }

    public function testGetTotalIgnoresDeletedProducts(): void
    {
        $session = $this->createMock(SessionInterface::class);
        // User has product 99 in their session cart
        $session->expects($this->once())->method('get')->with('cart', [])->willReturn([99 => 1]);

        $productRepository = $this->createMock(ProductRepository::class);
        // But the database returns null (product deleted)
        $productRepository->expects($this->once())->method('find')->with(99)->willReturn(null);

        $cartService = $this->createCartService($session, $productRepository);

        // Total should be 0.0 because the ghost product was skipped, leaving the cart completely empty
        $this->assertSame(0.0, $cartService->getTotal());
    }

    public function testGetTotalReturnsZeroForEmptyCart(): void
    {
        $session = $this->createMock(SessionInterface::class);
        $session->expects($this->once())->method('get')->with('cart', [])->willReturn([]);

        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->expects($this->never())->method('find');

        $cartService = $this->createCartService($session, $productRepository);

        $this->assertSame(0.0, $cartService->getTotal());
    }

    public function testGetTotalWithValidAndGhostProducts(): void
    {
        $session = $this->createMock(SessionInterface::class);
        // Cart has one valid product (10) and one deleted product (99)
        $session->expects($this->once())->method('get')->with('cart', [])->willReturn([10 => 1, 99 => 1]);

        $validProduct = (new Product())->setPriceHT(10.0);

        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->expects($this->exactly(2))
            ->method('find')
            ->willReturnCallback(function (int $id) use ($validProduct) {
                return $id === 10 ? $validProduct : null;
            });

        $cartService = $this->createCartService($session, $productRepository);

        // 10.0 (valid product) + 15.0 (shipping) = 25.0
        // The ghost product is safely ignored!
        $this->assertSame(25.0, $cartService->getTotal());
    }
}