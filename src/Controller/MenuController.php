<?php

namespace App\Controller;

use App\Manager\CartManager;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    #[Route('/menu', name: 'menu')]
    public function index(ProductRepository $productRepository, CartManager $cartManager): Response
    {
        return $this->render('menu/index.html.twig', [
            'products' => $productRepository->findAll(),
            'cart' => $cartManager->getCurrentCart()
        ]);
    }
}
