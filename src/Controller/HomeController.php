<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route; 

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $productRepo, CategoryRepository $categoryRepo): Response
    {
        return $this->render('home/index.html.twig', [
            'latest_products' => $productRepo->findBy([], ['id' => 'DESC'], 3),
            'categories' => $categoryRepo->findAll(),
        ]);
    }
}
