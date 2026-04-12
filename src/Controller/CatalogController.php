<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/catalog')]
final class CatalogController extends AbstractController
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
    ) {
    }

    #[Route('/', name: 'catalog_index')]
    public function index(): Response
    {
        $categories = $this->categoryRepository->findAll();
        return $this->render('catalog/index.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/category/{id}', name: 'catalog_category')]
    public function category(Category $category): Response
    {
        return $this->render('catalog/category.html.twig', [
            'category' => $category
        ]);
    }

    #[Route('/product/{id}', name: 'catalog_product')]
    public function product(Product $product): Response
    {
        return $this->render('catalog/product.html.twig', [
            'product' => $product
        ]);
    }
}
