<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testHomepageShowsDefaultName(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Hello Symfony');
    }

    public function testHomepageShowsProvidedName(): void
    {
        $client = static::createClient();
        $client->request('GET', '/Nadhem');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Hello Nadhem');
    }
}






