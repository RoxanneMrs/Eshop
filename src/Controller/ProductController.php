<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\ImageService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/product')]
class ProductController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    // La page d'accueil de "Nos créations". 
    //Elle doit afficher toutes les catégories de mes produits ainsi que tous mes produits
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository,
        CategoryRepository $categoryRepository,  
        Request $request): Response
    {

        $filter = $request->query->get('filter'); // Récupère le paramètre 'filter' de la requête

        // Récupère les événements selon le filtre, s'il est défini
        if ($filter === 'ASC') {
            $products = $productRepository->findBy([], ['price' => 'ASC'], 8);
        } elseif ($filter === 'DESC') {
            $products = $productRepository->findBy([], ['price' => 'DESC'], 8);
        } else {
            // Par défaut, affiche tous les événements
            $products =$productRepository->findBy([], null, 8);
        }

        return $this->render('product/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'products' => $products,
            'filter' => $filter,
            'currentRoute' => 'app_product_index', // Ajoutez cette ligne
        ]);
    }


     // Cette route permet d'accéder aux produits liés à 1 catégorie à partir de ma nav
     #[Route('/{id_category}', name: 'app_get_product_by_category', methods: ['GET'])]
     public function getProductByCategory(EntityManagerInterface $entityManager, int $id_category, CategoryRepository $categoryRepository, Request $request, ProductRepository $productRepository): Response
     {
         $filter = $request->query->get('filter');
         $category = $categoryRepository->find($id_category);

        if ($filter === 'ASC') {
            $products = $productRepository->findBy(['category' => $category], ['price' => 'ASC'], 8);
        } elseif ($filter === 'DESC') {
            $products = $productRepository->findBy(['category' => $category], ['price' => 'DESC'], 8);
        } else {
            $products = $productRepository->findBy(['category' => $category], null, 8);
        }
    
         return $this->render('product/index.html.twig', [
             'products' => $products, 
             'categories' => $categoryRepository->findAll(),
             'filter' => $filter,
             'currentRoute' => 'app_get_product_by_category',
             'categoryId' => $id_category,
         ]);
     }

     // Action pour le chargement progressif des produits
     #[Route('/{categoryId}/load-more/{filter}', name: 'app_product_load_more', methods: ['POST'])]
     public function loadMore(ProductRepository $productRepository, Request $request, $filter, $categoryId): Response
     {
        try {
            // Récupération du lastProductId depuis la requête
            $lastProductId = $request->request->getInt('lastProductId');
            $lastProductPrice = $request->request->getInt('lastProductPrice');
            $categoryId = $request->request->getInt('categoryId');
            $limit = 8;

            // Déterminer si un filtre par prix est demandé
            if ($filter === null || $filter === 'none') {
                $order = 'none';
            } else if ($filter === 'ASC' || $filter === 'DESC') {
                $order = strtoupper($filter);
            }


            // $categoryId = $request->request->get('categoryId', null);
            // if ($categoryId !== null) {
            //     $categoryId = (int) $categoryId;
            // }

             // Ajout des logs pour déboguer
            $this->logger->info('Chargement des produits avec les paramètres suivants:', [
                'lastProductId' => $lastProductId,
                'lastProductPrice' => $lastProductPrice,
                'categoryId' => $categoryId,
                'order' => $order,
                'limit' => $limit,
            ]);

            $products = $productRepository->findProductsByCriteria($lastProductId, $categoryId, $lastProductPrice, $order, $limit);
       

            $productsData = [];

            foreach ($products as $product) {
                $productData = [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'picture' => $product->getPicture(),
                    'price'=> $product->getPrice(),
                    'category_id' => $product->getCategory() ? $product->getCategory()->getId() : null,
                    'category_name' => $product->getCategory() ? $product->getCategory()->getTitle() : null,
                ];

                $productsData[] = $productData;   
            }

            // Retourner les produits au format JSON
            return $this->json(['products' => $productsData]);
            
        } catch (\Exception $e) {

            // Retourner une réponse d'erreur
            return $this->json(['error' => 'Erreur lors du chargement des produits'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    




    // La page qui affiche 1 article détaillé
    #[Route('/product/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }


    // Cette route permet de sélectionner tous les produits d'une catégorie si on est déjà sur la page d'accueil des produits, sans recharger la page
    #[Route('/api/category/{id_category}', name: 'app_api_get_product_by_category', methods: ['GET'])]
    public function getProductByCategoryJson(EntityManagerInterface $entityManager, int $id_category, CategoryRepository $categoryRepository): JsonResponse
    {
        $products = $entityManager->getRepository(Product::class)->findBy(array("category" => $id_category));

        $productsData = [];
        foreach ($products as $product) {
            $productData = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'text' => $product->getText(),
            'picture' => $product->getPicture(),
            'price'=> $product->getPrice(),
            'category_id' => $product->getCategory() ? $product->getCategory()->getId() : null,
            'category_name' => $product->getCategory() ? $product->getCategory()->getTitle() : null,
            ];
            $productsData[] = $productData;
        }

        return new JsonResponse($productsData);
    }   


    // cette route a pour but de laisser la possibilité à mes utilisateurs de trier les produits par prix
    // #[Route('/{filter}', name: 'app_product_filter')]
    // public function getProductByFilter(ProductRepository $productRepository, CategoryRepository $categoryRepository, Request $request, string $filter): JsonResponse {

    //     $products = $productRepository->findProductByFilter($filter);

    //     if ($filter === 'asc') {
    //         usort($products, function($a, $b) {
    //             return $a->getPrice() - $b->getPrice();
    //         });
    //     } 
        
    //     if ($filter === 'desc') {
    //         usort($products, function($a, $b) {
    //             return $b->getPrice() - $a->getPrice();
    //         });
    //     }

    //     $productsData = [];

    //     foreach ($products as $product) {

    //         $productData = [
    //             'id' => $product->getId(),
    //             'name' => $product->getName(),
    //             'text' => $product->getText(),
    //             'picture' => $product->getPicture(),
    //             'price'=> $product->getPrice(),
    //             'category_id' => $product->getCategory() ? $product->getCategory()->getId() : null,
    //             'category_name' => $product->getCategory() ? $product->getCategory()->getTitle() : null,
    //         ];

    //         $productsData[] = $productData;
    //     }
        
    //     return new JsonResponse($productsData);
    // }

}