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

}
