<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    #[Route('/menu', name: 'menu')]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('menu/index.html.twig', [
            'products' => $productRepository->findAll()
        ]);
    }
}
