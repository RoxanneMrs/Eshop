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
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository, CategoryRepository $categoryRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $products = $paginator->paginate(
            $productRepository->findAll(),
            $request->query->getInt('page', 1),
            8 // nombre de produits par page
        );

        return $this->render('product/index.html.twig', [
            // 'products' => $productRepository->findAll(),
            'categories' => $categoryRepository->findAll(),
            'products' => $products,
        ]);
    }

    
    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }



    #[Route('/category/{id_category}', name: 'app_get_product_by_category', methods: ['GET'])]
    public function getProductByCategory(EntityManagerInterface $entityManager, int $id_category, CategoryRepository $categoryRepository): Response
    {
        //findBy methode prédefini, permet de recuperer des données en filtrant
        $products = $entityManager->getRepository(Product::class)->findBy(array("category" => $id_category));
        
        return $this->render('product/index.html.twig', [
            'products' => $products, 
            'categories' => $categoryRepository->findAll(),
        ]);
    }



    #[Route('/api/category/{id_category}', name: 'app_api_get_product_by_category', methods: ['GET'])]
    public function getProductByCategoryJson(EntityManagerInterface $entityManager, int $id_category, CategoryRepository $categoryRepository): JsonResponse
    {
        $products = $entityManager->getRepository(Product::class)->findBy(array("id_category" => $id_category));

        return new JsonResponse($products);
    }   





    #[Route('/category/{id_category}/filter/{filter}', name: 'app_get_filtered_product_by_category', methods: ['GET'])]
    public function getFilteredProductsByCategory(EntityManagerInterface $entityManager, int $id_category, string $filter): JsonResponse
    {
        $products = $entityManager->getRepository(Product::class)->findBy(array("category" => $id_category));

        // on trie les produits par rapport au filtre choisi 
        if ($filter === 'asc') {
            usort($products, function($a, $b) {
            return $a->getPrice() - $b->getPrice();
            });
        } else if ($filter === 'desc') {
            usort($products, function($a, $b) {
            return $b->getPrice() - $a->getPrice();
            });
        }

        // on met les infos des produits dans un tableau qui sera transformé en json
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

        // on transforme en json
        return new JsonResponse($productsData);
    }



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

            // on ajoute un tableau simplifié des produits au tableau des produits
            $productsData[] = $productData;
        }
        
        // on renvoie le tableau des produits en JSON
        return new JsonResponse($productsData);
    }

}
