<?php

namespace App\Controller;


use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class CommentController extends AbstractController
{
    #[Route('/comment', name: 'app_comment')]
    public function index(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, CommentRepository $commentRepository, PaginatorInterface $paginator): Response {
    
            $comment = new Comment;
            $form = $this->createForm(CommentType::class, $comment);
            $form->handleRequest($request);
    
            if($form->isSubmitted() && $form->isValid()) {
                    
                    $comment->setUser($this->getUser());
                    $note = $form->get('note')->getData();
                    $comment->setNote($note);
                    $comment->setDate(new \DateTime);
                    $comment->setValid(false);
                    $entityManager->persist($comment); // insérer en base
                    $entityManager->flush(); // fermer la transaction executée par la BDD
        
                    $this->addFlash(
                        'success',
                        'Votre commentaire a bien été envoyé'
                    );

                    return $this->redirectToRoute('app_comment');
            }
    
            $comments = $paginator->paginate(
                $commentRepository->findAll(),
                $request->query->getInt('page', 1),
                5 // nombre de commentaires par page
            );

            return $this->render('comment/index.html.twig', [
                'commentForm' => $form,
                'comments' => $comments,
            ]);
        }
}
