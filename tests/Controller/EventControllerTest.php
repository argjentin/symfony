<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EventControllerTest extends WebTestCase
{
    public function testEventCreate()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/event/create');
        $buttonCrawlerNode = $crawler->selectButton('Créer');

        $this->assertResponseIsSuccessful();
        
        $form = $buttonCrawlerNode->form([
            'event_form[title]' => 'Event Title',
            'event_form[description]' => 'Event Description',
            'event_form[datetime]' => '2021-01-01 00:00:00',
            'event_form[maxParticipants]' => 100,
            'event_form[public]' => true,
        ]);

        $client->submit($form);
        $this->assertResponseRedirects('/');
    }
}