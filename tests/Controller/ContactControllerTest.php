<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ContactControllerTest extends WebTestCase
{
    public function testContactFormIsRenderedOnHomePage(): void
    {
        $client = self::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('#contact');
        $this->assertSelectorExists('input[name="contact_flow[identity][lastName]"]');
        $this->assertSelectorExists('input[name="contact_flow[identity][email]"]');
    }

    public function testStep1ValidSubmitAdvancesToStep2(): void
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/');

        $form = $crawler->selectButton('contact_flow[navigator][next]')->form([
            'contact_flow[identity][lastName]' => 'Dupont',
            'contact_flow[identity][firstName]' => 'Jean',
            'contact_flow[identity][email]' => 'jean.dupont@example.com',
        ]);

        $client->submit($form);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('input[name="contact_flow[mission][location]"]');
    }

    public function testStep1InvalidSubmitStaysOnStep1(): void
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/');

        $form = $crawler->selectButton('contact_flow[navigator][next]')->form([
            'contact_flow[identity][lastName]' => '',
            'contact_flow[identity][firstName]' => '',
            'contact_flow[identity][email]' => 'invalid-email',
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorExists('input[name="contact_flow[identity][lastName]"]');
    }
}
