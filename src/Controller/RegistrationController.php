<?php

namespace App\Controller;

use App\Entity\CustomerAddress;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CustomerAddress $deliveryAddress */
            $deliveryAddress = $form->get('deliveryAddress')->getData();
            $deliveryAddress->setUserAccount($user);
            $deliveryAddress->setIsDelivery(true);

            $isSameAsDelivery = $form->get('sameAsDelivery')->getData();
            /** @var CustomerAddress $billingAddress */
            $billingAddress = $form->get('billingAddress')->getData();

            if ($isSameAsDelivery === true || ($billingAddress === null || $billingAddress->getCity() === null)) {
                $deliveryAddress->setIsBilling(true);
                $entityManager->persist($deliveryAddress);
            } else {
                $deliveryAddress->setIsBilling(false);
                $entityManager->persist($deliveryAddress);

                $billingAddress = $form->get('billingAddress')->getData();

                if ($billingAddress && $billingAddress->getCity() !== null) {
                    $billingAddress->setUserAccount($user);
                    $billingAddress->setIsDelivery(false);
                    $billingAddress->setIsBilling(true);

                    $entityManager->persist($billingAddress);
                }
            }

            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success','Votre compte a bien été créé');

            return $security->login($user, 'form_login', 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
