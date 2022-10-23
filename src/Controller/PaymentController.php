<?php

namespace App\Controller;

use App\Manager\CartManager;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController
{
    #[Route('/payment', name: 'payment')]
    public function index(): Response
    {
        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }

    #[Route('/checkout', name: 'payment')]
    public function checkout(CartManager $cartManager, $stripeSK): Response
    {
        \Stripe\Stripe::setApiKey($stripeSK);

        $cartItems = $cartManager->getCurrentCart()->getItems();
        $line_items = [];
        foreach ($cartItems as $cartItem) {
            //return new Response($cartItem->getProduct()->getName());
            $item =
                array(
                    "price_data" => array(
                        "currency" => "eur",
                        "product_data" => array(
                            "name" => $cartItem->getProduct()->getName(),
                        ),
                        "unit_amount" => $cartItem->getProduct()->getPrice() * 100),
                    "quantity" => $cartItem->getQuantity());

            $line_items[] = $item;
        }

        //need full URL for succes and cancel and not only path. This comes from Stripe servers redirecting to our server.
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items'           => $line_items,
            'mode' => 'payment',
            'success_url' => $this->generateUrl('success_url', ['test' => 'ywa'], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('cancel_url', ['test' => 'ywa'], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
        return $this->redirect($session->url, 303);
    }

    #[Route('/success-url', name: 'success_url')]
    public function successUrl(Request $request): Response
    {
        dd($request->get('test'));
        return $this->render('payment/success.html.twig', []);
    }


    #[Route('/cancel-url', name: 'cancel_url')]
    public function cancelUrl(Request $request): Response
    {
        dd($request->get('test'));
        return $this->render('payment/cancel.html.twig', []);
    }
}
