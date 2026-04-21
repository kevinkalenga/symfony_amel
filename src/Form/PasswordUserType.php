<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Length;

class PasswordUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('actualPassword', PasswordType::class, [
                'label' => "Votre mot de passe actuel",
                'attr' => [
                     'placeholder' => "Indiquez votre mot de passe acuel"
                ],
                'mapped' => false,
            ])
              ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                
                'constraints' => [
                    new Length(min: 4, max: 30)
                ],
                'first_options' => [
                      'label' => "Votre nouveau mot de passe",
                      'attr' => [
                            'placeholder' => "Chisissez votre nouveau mot de passe"
                    ],
                    'hash_property_path' => 'password',
                ],
                
              
                
                'second_options' => [
                      'label' => "Confirmez votre nouveau mot de passe",
                      'attr' => [
                            'placeholder' => "Confirmez votre nouveau mot de passe"
                    ]
                ],
                'mapped' => false,
                
              
            ])
             ->add('Submit', SubmitType::class, [
                'label' => "Mettre à jour mon mot de passe",
                'attr' => [
                     'class' => "btn btn-success"
                ]
            ])

            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event){
                // Chercher le formulaire 
                $form = $event->getForm();

                // Chercher le user actuel 
                $user = $form->getConfig()->getOptions()['data'];

                // Verif de l'encodage de mdp 

                $passwordHasher = $form->getConfig()->getOptions()['passwordHasher'];

                // Methode permettant de comparer le mdp de user et celui du formulaire 

                $isValid = $passwordHasher->isPasswordValid($user, $form->get('actualPassword')->getData());

                // Message d erreur 
                 if(!$isValid) {
                    $form->get('actualPassword')->addError(new FormError("Votre mot de passe n'est pas conforme. Veuillez verifier votre saisie"));
                 };
            })
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'passwordHasher' => null
        ]);
    }
}
