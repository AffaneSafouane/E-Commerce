<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Email(message: 'Veuillez entrer un email valide'),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type'=> PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'Les mots de passe doivent être identique',
                'attr' => ['autocomplete' => 'new-password'],
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmer le mot de passe'],
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez entrer un mot de passe.',
                    ),
                    new Length(
                        min: 6,
                        minMessage: 'Votre mot de passe doit contenir au moins {{ limit }} caractères.',
                        max: 4096,
                    ),
                ],
            ])
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez entrer un nom de famille.',
                    ),
                    new Length(
                        min: 3,
                        minMessage: 'Votre nom de famille doit contenir au moins {{ limit }} caractères.',
                        max: 255,
                    ),
                ],
                'label' => 'Nom de famille',
            ])
            ->add('firstName', TextType::class, [
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez entrer un prénom.',
                    ),
                    new Length(
                        min: 3,
                        minMessage: 'Votre prénom doit contenir au moins {{ limit }} caractères.',
                        max: 255,
                    ),
                ],
                'label' => 'Prénom',
            ])
            ->add('phone', TelType::class, [
                'required' => false,
                'constraints' => [
                    new Regex(
                        pattern: '/^\+33[1-9][0-9]{8}$/',
                        message: 'Veuillez entrer un numéro de téléphone français valide commençant par +33 (ex: +33612345678).',
                    )
                ],
                'attr' => [
                    'placeholder' => '+33612345678'
                ],
                'label' => 'Téléphone',
            ])
            ->add('deliveryAddress', CustomerAddressType::class, [
                'mapped' => false,
                'label' => 'Adresse de livraison',
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue(
                        message: 'Vous devez accepter nos conditions d\'utilisation.',
                    ),
                ],
                'label' => 'Conditions d\'utilisations',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
