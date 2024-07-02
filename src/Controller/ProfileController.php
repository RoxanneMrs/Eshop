<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Form\UserType;
use App\Service\ImageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        $user = $this->getUser();
        $orders = $user->getOrders();

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'orders' => $orders,
        ]);
    }

    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function modify(Request $request, EntityManagerInterface $entityManager, ImageService $imageService) : Response
    {

        $form = $this->createForm(UserType::class, $this->getUser());
        $form->handleRequest($request);


        if($form->isSubmitted()) {
            if($form->isValid()) {

                $user = $this->getUser();
                $directory = $this->getParameter('user_picture_directory');
                $newFilename = $imageService->copyImage('picture', $directory, $form);
        
                if ($newFilename) {
                    $user->setPicture($newFilename);
                }


                $entityManager->persist($this->getUser());
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    'Votre profil a bien été mis à jour'
                );

                return $this->redirectToRoute('app_profile', [], Response::HTTP_SEE_OTHER);
            }
        }

    

        return $this->render('profile/modify-profile.html.twig', [
            'controller_name' => 'ProfileController',
            'profileForm' => $form,
        ]);
    }

    #[Route('/profile/password/edit', name: 'app_profile_password_edit')]
    public function modifyPassword(Request $request, 
                                    EntityManagerInterface $entityManager, 
                                    UserPasswordHasherInterface $passwordEncoder): Response {

        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordFormType::class, $user);
        $form->handleRequest($request);


        if($form->isSubmitted()) {

            if ($passwordEncoder->isPasswordValid($user, $form['oldPassword']->getData())) {

                if($form->isValid()) {

                    $newEncodedPassword = $passwordEncoder->hashPassword($user, $form->get('password')->getData());
                    $user->setPassword($newEncodedPassword);

                    $entityManager->persist($this->getUser());
                    $entityManager->flush();

                    $this->addFlash(
                        'success',
                        'Votre mot de passe a bien été modifié'
                    );

                    return $this->redirectToRoute('app_profile', [], Response::HTTP_SEE_OTHER);
                }
            }
        }

        return $this->render('profile/modify-password.html.twig', [
            'ChangePasswordForm' => $form,
        ]);
    }


    #[Route('/profile/delete', name: 'app_profile_delete')]
    public function deleteUser(Request $request, EntityManagerInterface $entityManager, TokenStorageInterface $token): Response
    {

        $user = $this->getUser();
        $entityManager->remove($user);
        $entityManager->flush();
        $token->setToken(null);

        
        $this->addFlash(
            'success',
            'Votre compte a été supprimé'
        );

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }

}
