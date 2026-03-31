<?php

declare(strict_types=1);

namespace App\Tests\Controller;

final class HomeControllerTest extends AbstractControllerTest
{
    public function testIndexPage(): void
    {
        $client = self::createClient();
        $this->setupDependencies();
        $this->ensurePersonalInfo();

        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists('#about');
        $this->assertSelectorExists('#resume');
        $this->assertSelectorExists('#contact');
    }
}
