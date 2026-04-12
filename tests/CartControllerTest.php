<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\CustomerAddress;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CartControllerTest extends WebTestCase
{
    // Verifies that an anonymous user is redirected to the login page.
    public function testCartRedirectAnonymousUser(): void
    {
        $client = static::createClient();
        $client->request('GET', '/cart');

        $this->assertResponseRedirects('/login');
    }

    // Verifies that an authenticated user can access the checkout page. 
    public function testCartWithAuthenticatedUser(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        $userRepository = $container->get(UserRepository::class);
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        $email = 'authenticated-user@example.com';
        $testUser = $userRepository->findOneByEmail($email);

        if (!$testUser) {
            $testUser = new User();
            $testUser->setEmail($email);
            $testUser->setName('Dupont');
            $testUser->setFirstName('Jean');
            $testUser->setPhone('+33612345678');
            $testUser->setBirthDay(new \DateTime('-20 years'));

            // 1. Handle Password Hashing
            $hashedPassword = $passwordHasher->hashPassword($testUser, 'SecurePassword123');
            $testUser->setPassword($hashedPassword);

            // 2. Handle the Address (Required based on your registration form)
            $address = new CustomerAddress();
            $address->setName('Dupont');
            $address->setPhone('+33712345678');
            $address->setAddress('123 Rue de la Paix');
            $address->setPostalCode('75001');
            $address->setCity('Paris');
            $address->setCountry('FR');

            // Link address to user (adjust method name to your entity logic, e.g., setDeliveryAddress)
            $address->setIsBilling(true);
            $testUser->addCustomerAddress($address);

            $entityManager->persist($address);
            $entityManager->persist($testUser);
            $entityManager->flush();
        }

        // Act: Log in the user we just created/found
        $client->loginUser($testUser);

        // Request the cart page
        $client->request('GET', '/checkout');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Votre panier');
    }
}