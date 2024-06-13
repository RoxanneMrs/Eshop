<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FooterController extends AbstractController
{
    #[Route('/footer', name: 'app_footer')]
    public function index(): Response
    {
        return $this->render('footer/index.html.twig', [
            'controller_name' => 'FooterController',
        ]);
    }



    #[Route('/conditionsdevente', name: 'app_conditions')]
    public function showConditions(): Response
    {
        return $this->render('footer/terms_and_conditions.html.twig', [
            'controller_name' => 'FooterController',
        ]);
    }
}
