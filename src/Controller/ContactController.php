<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(
    Request $request,
    EntityManagerInterface $entityManager,
    ValidatorInterface $validator, 
    MailerInterface $mailer   
    ): Response {

        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request); // permet d'intercepter la requête lancée par la soumission du formulaire

        if($form->isSubmitted()) {

            if ($form->isValid()) {

                $contact->setDate(new \DateTime);
                $entityManager->persist($contact); // insérer en base
                $entityManager->flush(); // fermer la transaction executée par la BDD
    
                $this->addFlash(
                    'success',
                    'Votre message a bien été envoyé'
                );

                // $message = (new Email())
                $message = (new TemplatedEmail())
                    ->from($this->getParameter('contact-marais@borisdymak.fr'))
                    ->to($this->getParameter('contact-marais@borisdymak.fr'))
                    ->cc($contact->getEmail())
                    ->subject($contact->getTopic())
                    ->text($contact->getMessage())
                    // ->html('<p>' . $contact->getMessage() .' </p>');
                    ->htmlTemplate("email/contact.html.twig")
                    ->context(['contact' => $contact,]);
                $mailer->send($message);
    

                // rediriger vers une autre page
                // return $this->redirectToRoute(/* ... */);
            }
                    
                // else {

                // $errors = $validator->validate($contact);
                // dd($form->getErrors()); // c'est un var_dump
                
                // }

        }

        return $this->render('contact/index.html.twig', [
            'contactForm' => $form,
            'errors' => !isset($errors) ? null : $errors // si j'ai pas d'erreur n'affiche rien, sinon affiche errors
        ]);
    }
}
