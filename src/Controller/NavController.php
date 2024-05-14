<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NavController extends AbstractController
{

    // Controller de ma nav pour l'afficher sur toutes mes pages avec ma boucle for pour mettre le nom de mes catégories
    #[Route('/nav', name: 'app_nav')]
    public function index(CategoryRepository $categoryRepository): Response
    {

        $categories = $categoryRepository->findAll();

        return $this->render('nav.html.twig', [
            'categories' => $categories,
        ]);
    }
}
