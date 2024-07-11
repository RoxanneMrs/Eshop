<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(Request $request): Response
    {
        // récup les éléments du panier depuis la session
        $session = $request->getSession();
        $cartTotal = 0;

        // si il y a session et items > alors il y a total
        if(!is_null($session->get('cart')) && count($session->get('cart')) > 0) {

            for($i = 0; $i < count($session->get('cart')["id"]); $i++) {
                $cartTotal += (float) $session->get('cart')["price"][$i] * $session->get('cart')["stock"][$i];
            }
        }

        return $this->render('cart/index.html.twig', [
            'cartItems' => $session->get('cart'),
            'cartTotal' => $cartTotal,
        ]);
    }



    #[Route('/cart/{idProduct}', name: 'app_cart_add', methods: ['POST'])]
    public function addProduct(Request $request, ProductRepository $productRepository, int $idProduct): Response {

        //créer la session
        $session = $request->getSession();

        // si la session n'existe pas, je la crée
        if(!$session->get('cart')) {
            $session->set('cart', [
                "id" => [],
                "name" => [],
                "text" => [],
                "picture" => [],    
                "price" => [],
                "stock" => [],
                "priceIdStripe" => [],
            ]);
        }

        $cart = $session->get('cart');

        //ajouter le produit au panier
        //récupérer les infos du produit en BDD et l'ajouter à mon panier
        $product = $productRepository->find($idProduct);

        $quantity = $request->request->getInt('quantity');
        if ($quantity <= 0 || $quantity > $product->getStock()) {
            $this->addFlash('error', 'Ce produit n\'est disponible qu\'en ' . $product->getStock() . ' exemplaires maximum.');
            return $this->redirectToRoute('app_product_show', ['id'=> $idProduct]);
        }

        $cart["id"][] = $product->getId();
        $cart["name"][] = $product->getName();
        $cart["text"][] = $product->getText();
        $cart["picture"][] = $product->getPicture();
        $cart["price"][] = $product->getPrice();
        $cart["priceIdStripe"][] = $product->getPriceIdStripe();
        $cart["stock"][] = $quantity;
        // $cart["priceIdStripe"][] = $product->getPriceIdStripe();

        $session->set('cart', $cart);

        // $this->addFlash('success', 'Le produit a été ajouté à votre panier.');
    
        //calculer le montant total de mon panier
        // $cartTotal = 0;

        // for($i = 0; $i < count($session->get('cart')["id"]); $i++) {
        //     $cartTotal += floatval($session->get('cart')["price"][$i]) * $session->get('cart')["stock"][$i];
        // }

        // afficher la page panier
        // return $this->render('cart/index.html.twig', [
        //     'cartItems' => $session->get('cart'),
        //     'cartTotal' => $cartTotal,
        // ]);

        // je récupère l'url de la page précédente et redirige vers celle-ci si possible
        $referer = $request->headers->get('referer');
        return $this->redirect($referer ?: $this->generateUrl('app_home'));
    }


    #[Route('/cart/delete/{idProduct}', name: 'app_cart_remove', methods: ['POST'])]
    public function removeProduct(Request $request, int $idProduct): Response
    {
        $session = $request->getSession();

        $cartItems = $session->get('cart', [
            "id" => [], "name" => [], "text" => [], "picture" => [], 
            "price" => [], "stock" => [], "priceIdStripe" => []
        ]);
    
        // Collecter les index des articles à supprimer
        $indexesToRemove = [];
        foreach ($cartItems['id'] as $i => $id) {
            if ($id == $idProduct) {
                $indexesToRemove[] = $i;
            }
        }
    
        // Supprimer les articles par leurs index
        foreach ($indexesToRemove as $index) {
            foreach ($cartItems as $key => $items) {
                unset($cartItems[$key][$index]);
            }
        }
    
        // Réindexer les tableaux pour éviter des clés manquantes
        foreach ($cartItems as $key => $items) {
            $cartItems[$key] = array_values($cartItems[$key]);
        }
    
        $session->set('cart', $cartItems);
    
        $this->addFlash('success', 'Article(s) supprimé(s) du panier');
    
        return $this->redirectToRoute('app_cart');
    }
 


    #[Route('/cart/delete', name: 'app_cart_delete', methods: ['GET'])]
   public function deleteCart (Request $request): Response {

    $session = $request->getSession();
    $session->remove('cart', []);

    return $this->redirectToRoute('app_cart');

   }

}
