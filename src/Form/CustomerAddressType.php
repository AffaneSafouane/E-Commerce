<?php

namespace App\Form;

use App\Entity\CustomerAddress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class CustomerAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(message: 'Veuillez entrer un nom pour la livraison'),
                    new Length(
                        min: 3,
                        minMessage: 'Votre nom doit contenir au moins {{ limit }} caractères.',
                        max: 255,
                    )
                ],
                'label' => 'Nom',
            ])
            ->add('phone', TelType::class, [
                'constraints' => [
                    new NotBlank(message: 'Veuillez entrer un numéro de téléphone pour la livraison'),
                    new Regex(
                        pattern: '/^\+(?!330)[1-9]\d{7,14}$/',
                        message: 'Veuillez entrer un numéro de téléphone valide commençant par un code international (ex: +33612345678).',
                    )
                ],
                'label' => 'Téléphone',
            ])
            ->add('address', TextType::class, [
                'constraints' => [
                    new NotBlank(message: 'Veuillez entrer votre adresse'),
                    new Regex(
                        pattern: '/^\d+\s+.+$/',
                        message: 'Votre adresse doit commencer par un numéro (ex: 12 Rue de la Paix).'
                    )
                ],
                'label' => 'Adresse',
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code postal',
                'constraints' => [
                    new NotBlank(message: 'Veuillez entrer un code postal'),
                    new Length(
                        min: 2,
                        minMessage: 'Votre code postal doit contenir au moins {{ limit }} caractères.',
                        max: 5,
                        maxMessage: 'Votre code postal doit contenir au plus {{ limit }} caractères.',
                    ),
                    new Regex(
                        pattern: '/^\d+$/',
                        message: 'Votre code postal doit contenir uniquement des chiffres.'
                    )
                ],
            ])
            ->add('city', TextType::class, [
                'constraints' => [
                    new NotBlank(message: 'Veuillez entrer une ville'),
                    new Length(
                        maxMessage: 'Votre ville doit contenir au plus {{ limit }} caractères.',
                        max: 100,
                    )
                ],
                'label' => 'Ville',
            ])
            ->add('country', CountryType::class, [
                'preferred_choices' => ['FR'],
                'label' => 'Pays',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CustomerAddress::class,
        ]);
    }
}
