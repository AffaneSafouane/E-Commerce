<?php

namespace App\Tests;

use App\Entity\User;
use App\Entity\CustomerAddress;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileControllerTest extends WebTestCase
{
    // Test that accessing /profile redirects to /login for unauthenticated users
    public function testProfileRedirectsToLoginForAnonymousUsers(): void
    {
        $client = static::createClient();
        $client->request('GET', '/profil');

        $this->assertResponseRedirects('/login');
    }

    public function testProfileUpdateIsSuccessful(): void
    {
        $client = static::createClient();
        $container = static::getContainer();
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $userRepository = $container->get(UserRepository::class);
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        // 1. Find or create the test user
        $email = 'profile-test@example.com';
        $testUser = $userRepository->findOneByEmail($email);

        if (!$testUser) {
            $testUser = new User();

            $testUser->setEmail($email);
            $testUser->setName('OriginalName');
            $testUser->setFirstName('OriginalFirstName');
            $testUser->setPhone('+33600000000');
            $testUser->setBirthDay(new \DateTime('-25 years'));
            $testUser->setPassword($passwordHasher->hashPassword($testUser, 'password123'));

            // Optional: Add address if your profile/user logic requires it
            $address = new CustomerAddress();
            $address->setName('Home');
            $address->setAddress('10 Main St');
            $address->setCity('Paris');
            $address->setPostalCode('75000');
            $address->setCountry('FR');
            $address->setPhone('+33612345789');
            $address->setIsBilling(true);
            $address->setIsDelivery(true);
            $testUser->addCustomerAddress($address);

            $entityManager->persist($address);
            $entityManager->persist($testUser);
            $entityManager->flush();
        }

        // 2. Log that user in
        $client->loginUser($testUser);

        // 3. GET request to /profile and assert 200 OK
        $crawler = $client->request('GET', '/profil/edit');
        $this->assertResponseIsSuccessful();

        // 4. Submit the profile update form
        // We find the button (e.g., 'Enregistrer') to get the form object
        $form = $crawler->selectButton('Modifier')->form();

        $form['profile[firstName]'] = 'NewFirstName';
        $form['profile[email]'] = 'updated-email@example.com';

        $client->submit($form);

        // 5. Follow redirect and assert success flash message
        $this->assertResponseRedirects();
        $crawler = $client->followRedirect();

        // Check for a common flash message selector (usually .alert-success in Bootstrap)
        // Adjust the text to match what your controller actually sends
        $this->assertSelectorExists('.alert-success');
        $this->assertSelectorTextContains('.alert-success', 'Votre profil a bien été mis à jour.');

        // Final check: Verify the DB was actually updated
        $updatedUser = $userRepository->findOneByEmail('updated-email@example.com');
        $this->assertNotNull($updatedUser);
        $this->assertSame('NewFirstName', $updatedUser->getFirstName());
    }

    public function testChangePasswordIsSuccessful(): void
    {
        $client = static::createClient();
        $container = static::getContainer();
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $userRepository = $container->get(UserRepository::class);
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        // --- 1. User Creation Setup (Same as before) ---
        $email = 'password-test@example.com';
        $testUser = $userRepository->findOneByEmail($email);

        if (!$testUser) {
            $testUser = new User();
            $testUser->setEmail($email);
            $testUser->setName('Name');
            $testUser->setFirstName('First');
            $testUser->setBirthDay(new \DateTime('-25 years'));
            $testUser->setPassword($passwordHasher->hashPassword($testUser, 'OldPassword123'));

            $address = new CustomerAddress();
            $address->setName('Maison');
            $address->setAddress('123 Test');
            $address->setCity('Paris');
            $address->setPostalCode('75000');
            $address->setPhone('+33612345789');
            $address->setCountry('FR');
            $address->setIsDelivery(true);
            $address->setIsBilling(true);
            $testUser->addCustomerAddress($address);

            $entityManager->persist($address);
            $entityManager->persist($testUser);
            $entityManager->flush();
        }

        // --- 2. The Test Logic ---
        $client->loginUser($testUser);

        // Request the change password page
        $crawler = $client->request('GET', '/profil/password');
        $this->assertResponseIsSuccessful();

        // Find the form. CHANGE 'Enregistrer' TO YOUR ACTUAL BUTTON TEXT!
        $form = $crawler->selectButton('Modifier')->form();

        $form['change_paswword[currentPassword]'] = 'OldPassword123';

        $form['change_paswword[newPassword][first]'] = 'NewSecurePassword456';
        $form['change_paswword[newPassword][second]'] = 'NewSecurePassword456';

        $client->submit($form);

        // Assert redirect to profile show page
        $this->assertResponseRedirects('/profil/');
        $client->followRedirect();

        // Verify the flash message exists
        $this->assertSelectorTextContains('.alert-success', 'Votre mot de passe a bien été modifié');

        // Verify the database actually hashed and saved the new password
        $updatedUser = $userRepository->findOneByEmail($email);
        $this->assertTrue($passwordHasher->isPasswordValid($updatedUser, 'NewSecurePassword456'));
    }

    public function testDeleteProfileIsSuccessful(): void
    {
        $client = static::createClient();
        $container = static::getContainer();
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $userRepository = $container->get(UserRepository::class);
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        // --- 1. User Creation Setup ---
        $email = 'delete-test@example.com';
        $testUser = $userRepository->findOneByEmail($email);

        if (!$testUser) {
            $testUser = new User();
            $testUser->setEmail($email);
            $testUser->setName('Name');
            $testUser->setFirstName('First');
            $testUser->setBirthDay(new \DateTime('-25 years'));
            $testUser->setPassword($passwordHasher->hashPassword($testUser, 'password123'));

            $address = new CustomerAddress();
            $address->setName('Maison');
            $address->setAddress('123 Test');
            $address->setCity('Paris');
            $address->setPostalCode('75000');
            $address->setCountry('FR');
            $address->setPhone('+33612345789');
            $address->setIsDelivery(true);
            $address->setIsBilling(true);
            $testUser->addCustomerAddress($address);

            $entityManager->persist($address);
            $entityManager->persist($testUser);
            $entityManager->flush();
        }

        $userId = $testUser->getId(); // Save the ID to check later

        // --- 2. The Test Logic ---
        $client->loginUser($testUser);

        // Go to the page where the delete form exists
        $crawler = $client->request('GET', '/profil/');
        $this->assertResponseIsSuccessful();

        // Submit the delete form. CHANGE 'Supprimer' TO YOUR ACTUAL BUTTON TEXT!
        $form = $crawler->selectButton('Supprimer')->form();
        $client->submit($form);

        // Assert redirect to the home page ('app_home')
        $this->assertResponseRedirects('/');
        $client->followRedirect();

        // Clear the entity manager so it's forced to fetch fresh from the database
        $entityManager->clear();

        // Verify the user no longer exists in the database
        $deletedUser = $userRepository->find($userId);
        $this->assertNull($deletedUser, 'The user should have been deleted from the database.');
    }
}
