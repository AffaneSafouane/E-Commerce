<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class RegistrationControllerTest extends WebTestCase
{
    public function testSuccessfulRegistrationWithValidData(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        // Create today's date - 18 years to ensure valid age
        $birthDate = new \DateTime('-18 years');

        $crawler = $client->request('GET', '/register');

        $form = $crawler->selectButton('S\'inscrire')->form();

        $form['registration_form[email]'] = 'newuser@example.com';
        $form['registration_form[plainPassword][first]'] = 'SecurePassword123';
        $form['registration_form[plainPassword][second]'] = 'SecurePassword123';
        $form['registration_form[name]'] = 'Dupont';
        $form['registration_form[firstName]'] = 'Jean';
        $form['registration_form[phone]'] = '+33612345678';
        $form['registration_form[birthDay]'] = $birthDate->format('Y-m-d');
        $form['registration_form[deliveryAddress][name]'] = 'Dupont';
        $form['registration_form[deliveryAddress][phone]'] = '+33712345678';
        $form['registration_form[deliveryAddress][address]'] = '123 Rue de la Paix';
        $form['registration_form[deliveryAddress][postalCode]'] = '75001';
        $form['registration_form[deliveryAddress][city]'] = 'Paris';
        $form['registration_form[deliveryAddress][country]'] = 'FR';
        $form['registration_form[sameAsDelivery]'] = true;
        $form['registration_form[agreeTerms]'] = true;

        $client->submit($form);

        $this->assertResponseIsSuccessful();
        
        $this->assertSelectorTextContains('h1', 'Hello HomeController!');
        $this->assertSelectorExists('a[href="/logout"]');

        // Verify user was created with correct data
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'newuser@example.com']);

        $this->assertNotNull($user);
        $this->assertSame('newuser@example.com', $user->getEmail());
        $this->assertSame('Dupont', $user->getName());
        $this->assertSame('Jean', $user->getFirstName());
        $this->assertSame('+33612345678', $user->getPhone());
    }

    public function testRegistrationFailsWithUserUnder18(): void
    {
        $client = static::createClient();

        // Create a birthdate that makes the user under 18
        $birthDate = new \DateTime('-17 years');

        $crawler = $client->request('GET', '/register');

        $form = $crawler->selectButton('S\'inscrire')->form();

        $form['registration_form[email]'] = 'underage@example.com';
        $form['registration_form[plainPassword][first]'] = 'SecurePassword123';
        $form['registration_form[plainPassword][second]'] = 'SecurePassword123';
        $form['registration_form[name]'] = 'Durand';
        $form['registration_form[firstName]'] = 'Michel';
        $form['registration_form[phone]'] = '+33687654321';
        $form['registration_form[birthDay]'] = $birthDate->format('Y-m-d');
        $form['registration_form[deliveryAddress][name]'] = 'Durand';
        $form['registration_form[deliveryAddress][phone]'] = '+33787654321';
        $form['registration_form[deliveryAddress][address]'] = '456 Avenue des Champs';
        $form['registration_form[deliveryAddress][postalCode]'] = '75008';
        $form['registration_form[deliveryAddress][city]'] = 'Paris';
        $form['registration_form[deliveryAddress][country]'] = 'FR';
        $form['registration_form[sameAsDelivery]'] = true;
        $form['registration_form[agreeTerms]'] = true;

        $crawler = $client->submit($form);

        // Form should not be valid, so we should see form errors
        $this->assertSelectorTextContains('.invalid-feedback', 'Vous devez avoir au moins 18 ans pour vous inscrire.');

        // Verify user was not created
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'underage@example.com']);

        $this->assertNull($user);
    }
}
