<?php

namespace App\Form;

use App\Entity\CustomerAddress;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CustomerAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Livraison' => 'Delivery',
                    'Facturation' => 'Billing',
                ],
                'data' => 'Delivery', // Par défaut "Livraison" pour l'inscription
            ])
            ->add('firstName', TextType::class, [
                'constraints' => [
                    new NotBlank(message: 'Veuillez entrer un prénom pour la livraison')
                ],
                'label' => 'Prénom',
            ])
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(message: 'Veuillez entrer un nom de famille pour la livraison')
                ],
                'label' => 'Nom',
            ])
            ->add('phone', TelType::class, [
                'required' => false,
                'label' => 'Téléphone',
            ])
            ->add('address', TextType::class, [
                'constraints' => [
                    new NotBlank(message: 'Veuillez entrer votre adresse')
                ],
                'label' => 'Adresse',
            ])
            ->add('cp', TextType::class, [
                'label' => 'Code postal',
                'constraints' => [
                    new NotBlank(message: 'Veuillez entrer un code postal')
                ],
            ])
            ->add('city', TextType::class, [
                'constraints' => [
                    new NotBlank(message: 'Veuillez entrer une ville')
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
