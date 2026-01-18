<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class HomeControllerTest extends WebTestCase
{
    public function testIndexPage(): void
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists('#about');
        $this->assertSelectorExists('#resume');
        $this->assertSelectorExists('#contact');
        $this->assertSelectorExists('#sf-contact-form');
    }
}
