<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(message: 'Veuillez entrer une adresse email'),
                    new Email(message: 'Veuillez entrer une adresse email valide'),
                ],
            ])
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(message: 'Veuillez entrer un nom de famille'),
                    new Length(min: 3, max: 255),
                ],
            ])
            ->add('firstName', TextType::class, [
                'constraints' => [
                    new NotBlank(message: 'Veuillez entrer un prénom'),
                    new Length(min: 3, max: 255),
                ],
            ])
            ->add('phone', TelType::class, [
                'required' => false,
                'constraints' => [
                    new Regex(
                        pattern: '/^\+[1-9]\d{7,12}$/',
                        message: 'Veuillez entrer un numéro de téléphone valide.',
                    )
                ]
            ])
            ->add('deliveryAddress', CustomerAddressType::class, [
                'mapped' => false,
                'label' => 'Adresse de livraison',
            ])
            // Add the sameAsDelivery checkbox
            ->add('sameAsDelivery', CheckboxType::class, [
                'mapped' => false,
                'label' => 'Utiliser la même adresse pour la facturation',
                'required' => false,
            ])
            // Add the billing address
            ->add('billingAddress', CustomerAddressType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Adresse de facturation',
                'row_attr' => [
                    'id' => 'billing-address-container',
                    'style' => 'display: none;',
                ],
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            if (isset($data['sameAsDelivery']) && $data['sameAsDelivery'] === '1') {
                $data['billingAddress'] = $data['deliveryAddress'] ?? null;
                $event->setData($data);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}