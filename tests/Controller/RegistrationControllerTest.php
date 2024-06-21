<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    public function testShowRegister()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('div.text', 'Inscription');
    }

    public function testSubmitForm()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $buttonCrawlerNode = $crawler->selectButton('S\'inscrire');
        $form = $buttonCrawlerNode->form([
            'registration_form[email]' => 'test10@example.com',
            'registration_form[firstName]' => 'John',
            'registration_form[lastName]' => 'Doe',
            'registration_form[plainPassword][first]' => 'password123',
            'registration_form[plainPassword][second]' => 'password123',
        ]);

        $client->submit($form);
        $this->assertResponseRedirects('/profil'); 
    }
}