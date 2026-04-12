<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePaswwordType;
use App\Form\ProfileType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profil')]
#[IsGranted('ROLE_USER')]
final class ProfileController extends AbstractController
{
    #[Route('/', name: 'app_profil_show', methods: ['GET'])]
    public function show(): Response
    {
        return $this->render('profil/show.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/edit', name: 'app_profil_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // 1. Fetch current addresses
        $currentDelivery = $user->getDeliveryAddress();
        $currentBilling = $user->getBillingAddress();

        // Determine if they are currently sharing the same address object
        $isSameAsDelivery = false;
        if ($currentDelivery && $currentBilling && $currentDelivery->getId() === $currentBilling->getId()) {
            $isSameAsDelivery = true;
        } elseif (!$currentBilling && $currentDelivery) {
            // Fallback: if they only have delivery, assume it's also billing
            $isSameAsDelivery = true;
        }

        $form = $this->createForm(ProfileType::class, $user);

        // 2. Pre-fill the unmapped fields BEFORE handling the request
        if ($currentDelivery) {
            $form->get('deliveryAddress')->setData($currentDelivery);
        }
        if ($currentBilling && !$isSameAsDelivery) {
            $form->get('billingAddress')->setData($currentBilling);
        }
        $form->get('sameAsDelivery')->setData($isSameAsDelivery);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // 3. Extract the submitted unmapped data
            $delivery = $form->get('deliveryAddress')->getData();
            $billing = $form->get('billingAddress')->getData();
            $sameAsDeliverySubmitted = $form->get('sameAsDelivery')->getData();

            // Link delivery address to user if it's brand new
            if ($delivery && !$user->getDeliveryAddress() === $delivery) {
                $delivery->setUserAccount($user);
                $user->addCustomerAddress($delivery);
            }
            $delivery->setIsDelivery(true);

            // Handle the Billing logic
            if ($sameAsDeliverySubmitted) {
                // They checked the box: Delivery is BOTH
                $delivery->setIsBilling(true);

                // Clean up: If they previously had a separate billing address, remove it
                if ($currentBilling && $currentBilling->getId() !== $delivery->getId()) {
                    $user->removeCustomerAddress($currentBilling);
                    $entityManager->remove($currentBilling);
                }
            } else {
                // They unchecked the box: Delivery is ONLY delivery
                $delivery->setIsBilling(false);

                if ($billing) {
                    // Link billing address to user if it's brand new
                    if (!$user->getBillingAddress() === $billing) {
                        $billing->setUserAccount($user);
                        $user->addCustomerAddress($billing);
                    }
                    $billing->setIsBilling(true);
                    $billing->setIsDelivery(false);
                    $entityManager->persist($billing);
                }
            }

            $entityManager->persist($delivery);
            $entityManager->flush();

            $this->addFlash('success', 'Votre profil a bien été mis à jour.');
            return $this->redirectToRoute('app_profil_show');
        }

        return $this->render('profil/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/password', name: 'app_change_password')]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $form = $this->createForm(ChangePaswwordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('newPassword')->getData();

            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);

            $user->setPassword($hashedPassword);
            $em->flush();

            $this->addFlash('success', 'Votre mot de passe a bien été modifié.');
            return $this->redirectToRoute('app_profil_show');
        }

        return $this->render('profil/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete', name: 'app_profil_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }
}
