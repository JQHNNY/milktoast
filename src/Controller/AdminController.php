<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductFormType;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/product/add', name: 'addProduct')]
    public function addProduct(Request $request, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $product = new Product();

        $form = $this->createForm(ProductFormType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $productFormData = $request->request->all();
            $product->setName($productFormData['product_form']['name']);
            $product->setDescription($productFormData['product_form']['description']);
            $product->setPrice($productFormData['product_form']['price']);
            $product->setThumbnail($productFormData['product_form']['thumbnail']);

            $entityManager->persist($product);

            $entityManager->flush();
        }
        return $this->renderForm('admin/product/add.html.twig', [
            'form' => $form
        ]);
    }
}
