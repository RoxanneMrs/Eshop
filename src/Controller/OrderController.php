<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/orders', name: 'app_orders_history')]
    public function history(OrderRepository $orderRepository): Response
    {
        // Récupérer toutes les commandes de l'utilisateur connecté
        $user = $this->getUser();
        $orders = $orderRepository->findBy(['user' => $user], ['date' => 'DESC']);

        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/order/{id}', name: 'app_order_details')]
    public function details(Order $order): Response
    {
        // Assurez-vous que l'utilisateur connecté est bien le propriétaire de cette commande
        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette commande.');
        }

        return $this->render('order/order_details_history.html.twig', [
            'order' => $order,
        ]);
    }
}

