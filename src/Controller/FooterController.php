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
        ]);
    }

    #[Route('/mentionslegales', name: 'app_legal_notices')]
    public function showLegalNotices(): Response
    {
        return $this->render('footer/legal_notices.html.twig', [
        ]);
    }

    #[Route('/cookies', name: 'app_cookies')]
    public function showCookies(): Response
    {
        return $this->render('footer/cookies.html.twig', [
        ]);
    }

    #[Route('/about', name: 'app_about')]
    public function showAbout(): Response
    {
        return $this->render('footer/about.html.twig', [
        ]);
    }
}
