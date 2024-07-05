<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HeaderController extends AbstractController
{

    // Controller de ma nav pour l'afficher sur toutes mes pages avec ma boucle for pour mettre le nom de mes catégories
    #[Route('/header', name: 'app_header')]
    public function index(CategoryRepository $categoryRepository, Request $request): Response
    {

        $categories = $categoryRepository->findAll();

          // Compter le nombre d'articles dans le panier
          $session = $request->getSession();
          $cart = $session->get('cart', ["id" => [], "stock" => []]);
          $cartItemCount = array_sum($cart["stock"]);
  

        return $this->render('header.html.twig', [
            'categories' => $categories,
            'cartItemCount' => $cartItemCount,
        ]);
    }
}
