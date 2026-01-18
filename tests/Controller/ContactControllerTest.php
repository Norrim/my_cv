<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ContactControllerTest extends WebTestCase
{
    public function testSubmitContactForm(): void
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('#sf-contact-form button[type="submit"]')->form();
        $form['contact[name]'] = 'John Doe';
        $form['contact[email]'] = 'john@example.com';
        $form['contact[message]'] = 'Hello, this is a test message with enough length.';

        $client->submit($form);

        $this->assertResponseRedirects('/#contact');
        $client->followRedirect();

        $this->assertSelectorExists('.alert-success');
    }

    public function testSubmitContactFormInvalid(): void
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/');

        $form = $crawler->filter('#sf-contact-form button[type="submit"]')->form();
        $form['contact[name]'] = '';
        $form['contact[email]'] = 'invalid-email';
        $form['contact[message]'] = 'short';

        $client->submit($form);

        $this->assertResponseRedirects('/#contact');
        $client->followRedirect();

        $this->assertSelectorExists('.alert-danger');
    }
}
