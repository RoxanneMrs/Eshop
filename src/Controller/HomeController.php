<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $productRepository, PaginatorInterface $paginator, Request $request, CategoryRepository $categoryRepository, CommentRepository $commentRepository): Response
    {
        $products = $paginator->paginate(
            $productRepository->findAll(),
            $request->query->getInt('page', 1), 
            5 // nombre de produits par page
        );

        $newProducts = $productRepository->findBy(['new' => true]);

        $comments = $paginator->paginate(
            $commentRepository->findBy(['note' => 5]),
            $request->query->getInt('page', 1),
            5 // nombre de commentaires par page
        );


        return $this->render('home/index.html.twig', [
            'products' => $products,
            'newProducts' => $newProducts,
            'categories' => $categoryRepository->findAll(),
            'comments' => $comments,
        ]);
    }



    #[Route('/search', name: 'app_search_products', methods: ['GET'])]
    public function getProductBySearch(ProductRepository $productRepository, Request $request, PaginatorInterface $paginator, CategoryRepository $categoryRepository): Response
    {

        // si j'ai un param GET search
        if($request->query->has("search")) {

            $search = strtolower($request->query->get("search"));
         
            $products = $paginator->paginate(
            $productRepository->findProductBySearch($search), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            8 /*limit per page*/
            );


            return $this->render('product/index.html.twig', [
                'products' => $products,
                'categories' => $categoryRepository->findAll(),
            ]);
        
        } else {
            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }
    }

}
