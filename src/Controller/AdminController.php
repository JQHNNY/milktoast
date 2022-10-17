<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\AddToCartType;
use App\Form\ProductFormType;

use App\Manager\CartManager;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'products' => $productRepository->findAll()
        ]);
    }

    #[Route('/admin/product/add', name: 'addProduct')]
    public function addProduct(Request $request, ManagerRegistry $doctrine, ProductRepository $productRepository): Response
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

            $entityManager->persist($product);

            $entityManager->flush();
            return $this->redirectToRoute('admin');
        }
        return $this->renderForm('admin/product/add.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/admin/product/update/{id}', name: 'updateProduct')]
    public function updateProduct(Request $request, ManagerRegistry $doctrine, ProductRepository $productRepository): Response
    {
        $entityManager = $doctrine->getManager();
        $product = $productRepository->getProductById($request->get('id'));

        $form = $this->createForm(ProductFormType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $productFormData = $request->request->all();
            $product->setName($productFormData['product_form']['name']);
            $product->setDescription($productFormData['product_form']['description']);
            $product->setPrice($productFormData['product_form']['price']);

            $entityManager->persist($product);

            $entityManager->flush();
            return $this->redirectToRoute('admin');
        }
        return $this->renderForm('admin/product/add.html.twig', [
            'product' => $product,
            'form' => $form
        ]);
    }

    #[Route('/admin/product/delete/{id}', name: 'deleteProduct')]
    public function deleteProduct(Request $request, ManagerRegistry $doctrine, ProductRepository $productRepository): Response
    {
        $entityManager = $doctrine->getManager();
        $product = $productRepository->getProductById($request->get('id'));

        $entityManager->remove($product);
        $entityManager->flush();

        return $this->redirectToRoute('admin');
    }

    #[Route('/admin/product/detail/{id}', name: 'detailProduct')]
    public function detail(Request $request, ProductRepository $productRepository, CartManager $cartManager): Response
    {
        $product = $productRepository->getProductById($request->get('id'));

        $form = $this->createForm(AddToCartType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item = $form->getData();
            $item->setProduct($product);

            $cart = $cartManager->getCurrentCart();
            $cart
                ->addItem($item)
                ->setUpdatedAt(new \DateTime());

            $cartManager->save($cart);

            return $this->redirectToRoute('detailProduct', ['id' => $product->getId()]);
        }

        return $this->renderForm('admin/product/detail.html.twig', [
            'product' => $product,
            'form' => $form
        ]);

    }
}
