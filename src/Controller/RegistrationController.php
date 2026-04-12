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
            /** @var CustomerAddress $delivery */
            $delivery = $form->get('deliveryAddress')->getData();
            $delivery->setUserAccount($user);
            $delivery->setIsDelivery(true);

            $isSameAsDelivery = $form->get('sameAsDelivery')->getData();

            /** @var CustomerAddress $billing */
            $billing = $form->get('billingAddress')->getData();

            if (!$isSameAsDelivery && $billing && $billing !== $delivery) {
                // They are different: Delivery is ONLY delivery
                $delivery->setIsBilling(false);

                $billing->setUserAccount($user);
                $billing->setIsBilling(true);
                $billing->setIsDelivery(false);
                $entityManager->persist($billing);
            } else {
                // They are the same: Delivery is BOTH
                $delivery->setIsBilling(true);
            }

            $entityManager->persist($delivery);

            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte a bien été créé');

            return $security->login($user, 'form_login', 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
