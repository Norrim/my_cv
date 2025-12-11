<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class HomePageTest extends WebTestCase
{
    public function testPlaceholderHomepageIsDisplayed(): void
    {
        $client = self::createClient();
        $client->request('GET', '/');

        // La page d’accueil par défaut n’existe pas => 404
        self::assertResponseStatusCodeSame(404);

        // Le placeholder affiche un <title> "Welcome to Symfony!"
        self::assertPageTitleContains('Welcome to Symfony');

        // Et un lien "Create your first page" vers la doc
        self::assertSelectorExists('a[href*="page_creation.html"]');
    }
}
