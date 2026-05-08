<?php 

namespace App\Classe;
use Symfony\Component\HttpFoundation\RequestStack;

class Cart 
{
    //  RequestStack permet d'acceder à la session de l'utiilisateur  
    public function __construct(private RequestStack $requestStack)
    {}
     
    // Ajout du produit dans le panier si il existe ou pas
    public function add($product) {
      //  Recup de panier stocké en session 
      $cart = $this->requestStack->getSession()->get('cart', []);
      //  verifie si le produit existe deja dans le panier 
      if(isset($cart[$product->getId()]))  {
        // Si le produit existe on augment la quantité
        $cart[$product->getId()] = [
            'object' => $product,
            'qty' => $cart[$product->getId()]['qty'] + 1
        ];
      } else {
        // Si le produit n'existe pas on l'ajoute avec qty=1
        $cart[$product->getId()] = [
            'object' => $product,
            'qty' => 1
        ];
      }

      // Sauvegarder le panier 
      $this->requestStack->getSession()->set('cart', $cart);
    }

    // fonction permettant de supprimer totalement le panier
    public function decreaseCart($id)
    {
        //  Recup de panier stocké en session 
         $cart = $this->requestStack->getSession()->get('cart', []);
         if($cart[$id]['qty'] > 1) {
             $cart[$id]['qty'] = $cart[$id]['qty']-1;
         } else {
            unset($cart[$id]);
         }

          // Sauvegarder le panier 
         $this->requestStack->getSession()->set('cart', $cart);   
    }
    // fonction permettant de supprimer totalement le panier
    public function removeCart()
    {
      return $this->requestStack->getSession()->remove('cart', []);
    }
    // fonction permettant de retourner le panier en cours
    public function getCart()
    {
      return $this->requestStack->getSession()->get('cart', []);
    }
}