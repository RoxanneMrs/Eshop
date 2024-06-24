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

    // La page d'accueil de "Nos créations". 
    //Elle doit afficher toutes les catégories de mes produits ainsi que tous mes produits
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository, CategoryRepository $categoryRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $products = $paginator->paginate(
            $productRepository->findAll(),
            $request->query->getInt('page', 1),
            8 // nombre de produits par page
        );

        return $this->render('product/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'products' => $products,
        ]);
    }



    // La page qui affiche 1 article détaillé
    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }



    // Cette route permet d'accéder aux produits liés à 1 catégorie à partir de ma nav
    #[Route('/category/{id_category}', name: 'app_get_product_by_category', methods: ['GET'])]
    public function getProductByCategory(EntityManagerInterface $entityManager, int $id_category, CategoryRepository $categoryRepository, PaginatorInterface $paginator, Request $request, ProductRepository $productRepository): Response
    {
        $products = $paginator->paginate(
            $productRepository->findBy(array("category" => $id_category)),
            $request->query->getInt('page', 1),
            8 // nombre de produits par page
        );
   
        return $this->render('product/index.html.twig', [
            'products' => $products, 
            'categories' => $categoryRepository->findAll(),
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



    // #[Route('/category/{id_category}/filter/{filter}', name: 'app_get_filtered_product_by_category', methods: ['GET'])]
    // public function getFilteredProductByCategory(EntityManagerInterface $entityManager, int $id_category, string $filter): JsonResponse
    // {
    //     $products = $entityManager->getRepository(Product::class)->findBy(array("category" => $id_category));

    //     // on trie les produits par rapport au filtre choisi 
    //     if ($filter === 'asc') {
    //         usort($products, function($a, $b) {
    //         return $a->getPrice() - $b->getPrice();
    //         });
    //     } else if ($filter === 'desc') {
    //         usort($products, function($a, $b) {
    //         return $b->getPrice() - $a->getPrice();
    //         });
    //     }

    //     $productsData = [];
    //     foreach ($products as $product) {
    //         $productData = [
    //         'id' => $product->getId(),
    //         'name' => $product->getName(),
    //         'text' => $product->getText(),
    //         'picture' => $product->getPicture(),
    //         'price'=> $product->getPrice(),
    //         'category_id' => $product->getCategory() ? $product->getCategory()->getId() : null,
    //         'category_name' => $product->getCategory() ? $product->getCategory()->getTitle() : null,
    //         ];
    //         $productsData[] = $productData;
    //     }

    //     return new JsonResponse($productsData);
    // }



    // cette route a pour but de laisser la possibilité à mes utilisateurs de trier les produits par prix
    #[Route('/filter/{filter}', name: 'app_product_filter')]
    public function getProductByFilter(ProductRepository $productRepository, CategoryRepository $categoryRepository, Request $request, string $filter): JsonResponse {
    
        $products = $productRepository->findProductByFilter($filter);

        if ($filter === 'asc') {
            usort($products, function($a, $b) {
                return $a->getPrice() - $b->getPrice();
            });
        } else {
            usort($products, function($a, $b) {
                return $b->getPrice() - $a->getPrice();
            });
        }

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

}
