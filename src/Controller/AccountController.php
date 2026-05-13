<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Form\PasswordUserType;
use App\Entity\Address;
use App\Repository\AddressRepository;
use App\Form\AddressUserType;

final class AccountController extends AbstractController
{
    
    private $entityManager; 


    
    
    
    
    public function __construct( EntityManagerInterface $entityManager)
    {
      $this->entityManager = $entityManager;
    }


    #[Route('/compte', name: 'app_account')]
    public function index(): Response
    {
        return $this->render('account/index.html.twig');
    }
    #[Route('/compte/modifier-mot-de-passe', name: 'app_account_modify_pwd')]
    public function password(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Recup de user connecté
        $user = $this->getUser();

        // Creation de form 
        $form = $this->createForm(PasswordUserType::class, $user, [
            'passwordHasher' => $passwordHasher
        ]);

        // Ecoute la requete 
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
              $this->addFlash(
               type:'success',
               message:'Votre mot de passe a été mis à jour avec succès'
           );
        }
    
        return $this->render('account/password.html.twig', [
            'modifyPwd' =>$form->createView()
        ]);
    }
    
    // Affichage du template pour les adresses
    #[Route('/compte/adresses', name: 'app_account_addresses')]
    public function addresses(): Response
    {
        return $this->render('account/addresses.html.twig');
    }
    
    // Ajout d'adresse
    #[Route('/compte/adresses/ajout/{id}', name: 'app_account_address_form', defaults: ['id' => null])]
    public function addressForm(Request $request, $id, AddressRepository $addressRepository): Response
    {
        if($id) {
            // L'adresse en bd
           $address = $addressRepository->findOneById($id);
           if(!$address or $address->getUser() != $this->getUser()) {
               return $this->redirectToRoute('app_account_addresses');
           }
        } else {
            $address = new Address();
            $address->setUser($this->getUser());
        }
        
        $form = $this->createForm(AddressUserType::class, $address);
        $form->handleRequest($request);

          if($form->isSubmitted() && $form->isValid()) {
             $this->entityManager->persist($address);
             $this->entityManager->flush();

             $this->addFlash(
               type:'success',
               message:'Votre adresse a été ajoutée avec succès'
            );

            return $this->redirectToRoute('app_account_addresses');
          }
        
        
        
        
        
        
        return $this->render('account/addressForm.html.twig', [
            'addressForm' => $form
        ]);
    }
    // Suppression d'adresse
    #[Route('/compte/adresses/suppression/{id}', name: 'app_account_address_delete')]
    public function deleteForm(Request $request, $id, AddressRepository $addressRepository): Response
    {
  
            // L'adresse en bd
           $address = $addressRepository->findOneById($id);
           
           if(!$address or $address->getUser() != $this->getUser()) {
               return $this->redirectToRoute('app_account_addresses');
           }

            $this->addFlash(
               type:'success',
               message:'Votre adress a été supprimée avec succès'
            );
      
            $this->entityManager->remove($address);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_account_addresses');    
        
       
    }
}
