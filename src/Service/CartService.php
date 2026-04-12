<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

final class CartService
{
    private const string CART_KEY = 'cart';
    private const float SHIPPING_FEE = 15.0;

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly ProductRepository $productRepository,
    ) {
    }

    public function add(int $id): void
    {
        $this->updateCart(function (array $cart) use ($id): array {
            $cart[$id] = ($cart[$id] ?? 0) + 1;

            return $cart;
        });
    }

    public function remove(int $id): void
    {
        $this->updateCart(function (array $cart) use ($id): array {
            unset($cart[$id]);

            return $cart;
        });
    }

    public function decrease(int $id): void
    {
        $this->updateCart(function (array $cart) use ($id): array {
            if (!isset($cart[$id])) {
                return $cart;
            }

            if ($cart[$id] <= 1) {
                unset($cart[$id]);
            } else {
                --$cart[$id];
            }

            return $cart;
        });
    }

    /**
     * @return array<int, array{product: Product, quantity: int}>
     */
    public function getFullCart(): array
    {
        $fullCart = [];

        foreach ($this->getCart() as $id => $quantity) {
            $product = $this->productRepository->find($id);

            if (!$product instanceof Product) {
                continue;
            }

            $fullCart[] = [
                'product' => $product,
                'quantity' => $quantity,
            ];
        }

        return $fullCart;
    }

    public function getTotal(): float
    {
        $fullCart = $this->getFullCart();

        if (empty($fullCart)) {
            return 0.0; 
        }

        $total = 0.0;
        foreach ($fullCart as $item) {
            $price = $item['product']->getPriceHT() ?? 0.0;
            $total += $price * $item['quantity'];
        }

        return $total + self::SHIPPING_FEE;
    }

    /**
     * Used for read-only operations (like getFullCart).
     * * @return array<int, int>
     */
    private function getCart(): array
    {
        $cart = $this->requestStack->getSession()->get(self::CART_KEY, []);

        return is_array($cart) ? $cart : [];
    }

    /**
     * Centralizes the read-modify-write cycle so the Session is only fetched once.
     *
     * @param callable(array<int, int>): array<int, int> $updater
     */
    private function updateCart(callable $updater): void
    {
        $session = $this->requestStack->getSession();

        $cart = $session->get(self::CART_KEY, []);
        if (!is_array($cart)) {
            $cart = [];
        }

        $updatedCart = $updater($cart);

        $session->set(self::CART_KEY, $updatedCart);
    }
}