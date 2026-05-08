<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Classe\Cart;
use App\Repository\ProductRepository;

final class CartController extends AbstractController
{
    #[Route('/mon-panier', name: 'app_cart')]
    public function index(Cart $cart): Response
    {
      
        return $this->render('cart/index.html.twig', [
            'cart' =>$cart->getCart(),
            'totalPrice' =>$cart->getTotalPrice()
        ]);
    }
    
    // La route permettant d'ajouter un produit dans le panier
    #[Route('/panier-ajout/{id}', name: 'app_cart_add')]
    public function add($id, Cart $cart, ProductRepository $productRepository, Request $request): Response
    {
           // resup de produit depuis la bd
            $product = $productRepository->findOneById($id);
            // Ajout dans le panier
            $cart->add($product);

            $this->addFlash(
               type:'success',
               message:'Votre produit a été ajouté avec succès.'
           );

          return $this->redirect($request->headers->get('referer'));
   
    }
    // La route permettant à la suppression du panier
    #[Route('/panier/supprimer', name: 'app_cart_remove')]
    public function remove(Cart $cart): Response
    {
           $cart->removeCart();

            $this->addFlash(
               type:'success',
               message:'Votre panier a été supprimé avec succès.'
           );
    
            return $this->redirectToRoute('app_home');
    }
    // La route permettant à la reduction de la quantité de produit du panier
    #[Route('/panier/reduction/{id}', name: 'app_cart_decrease')]
    public function decrease($id, Cart $cart): Response
    {
           $cart->decreaseCart($id);
    
            return $this->redirectToRoute('app_cart');
    }
}
