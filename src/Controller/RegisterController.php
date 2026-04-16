<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\RegisterUserType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

final class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        
        $user = new User();

        
    
        $form = $this->createForm(RegisterUserType::class, $user);

        // Ecoute la requette si le formulaire est soumis

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
        }
    
         return $this->render('register/index.html.twig', [
            'registerForm' => $form->createView()
         ]);
    }
}
