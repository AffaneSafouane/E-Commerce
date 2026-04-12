<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePaswwordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'mapped' => false,
                'label'=> 'Mot de passe actuel',
                'constraints' => [
                    new NotBlank(message: 'veuillez saisir votre mot de passe actuel'),
                    new UserPassword(message:'Le mot de passe actuel est incorrect.'),
                ]
            ])
            ->add('newPassword', RepeatedType::class, [
                'type'=> PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'Les nouveaux mots de passe doivent être identique',
                'attr' => ['autocomplete' => 'new-password'],
                'first_options' => ['label' => 'Nouveau Mot de passe'],
                'second_options' => ['label' => 'Confirmer le nouveau mot de passe'],
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez entrer votre nouveau mot de passe.',
                    ),
                    new Length(
                        min: 6,
                        minMessage: 'Votre mot de passe doit contenir au moins {{ limit }} caractères.',
                        max: 4096,
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
