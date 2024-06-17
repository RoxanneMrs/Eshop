<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\ImageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
            'categories' => $categoryRepository->findAll(),
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



    #[Route('/filter/{filter}', name: 'app_product_filter')]
    public function getProductByFilter(ProductRepository $productRepository, CategoryRepository $categoryRepository, Request $request, string $filter): JsonResponse {
        
        $productsData = [];

        foreach ($productRepository->findProductByFilter($filter) as $product) {

            $productData = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'text' => $product->getText(),
                'picture' => $product->getPicture(),
                'price'=> $product->getPrice(),
                'category_id' => $product->getCategory() ? $product->getCategory()->getId() : null,
                'category_name' => $product->getCategory() ? $product->getCategory()->getTitle() : null,
            ];

            // Ajouter le tableau simplifié de l'article au tableau des articles
            $productsData[] = $productData;
        }

        // Utilisez JsonResponse pour retourner le tableau d'articles en JSON
        return new JsonResponse($productsData);
    }

}
