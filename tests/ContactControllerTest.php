<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\PersonalInfo;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ContactControllerTest extends WebTestCase
{
    private function createRequiredData(): void
    {
        $container = self::getContainer();
        $em = $container->get('doctrine')->getManager();

        $personalInfo = new PersonalInfo();
        $personalInfo->setFirstname('John');
        $personalInfo->setName('Doe');
        $personalInfo->setTitle('Developer');
        $personalInfo->setEmail('john@doe.com');
        $personalInfo->setAbout('About John');

        $em->persist($personalInfo);
        $em->flush();
    }

    public function testSubmitContactForm(): void
    {
        $client = self::createClient();
        $this->createRequiredData();

        $crawler = $client->request('GET', '/');

        $form = $crawler->filter('button[type="submit"]')->form([
            'contact[name]' => 'John Doe',
            'contact[email]' => 'john@example.com',
            'contact[message]' => 'Hello, this is a test message with enough length.'
        ]);

        $client->submit($form);

        self::assertResponseRedirects('/#contact');
        $client->followRedirect();
        self::assertSelectorTextContains('.alert-success', 'Thanks! Your message has been sent.');
    }

    public function testSubmitContactFormWithInvalidData(): void
    {
        $client = self::createClient();
        $this->createRequiredData();

        $crawler = $client->request('GET', '/');

        $form = $crawler->filter('button[type="submit"]')->form([
            'contact[name]' => '',
            'contact[email]' => 'invalid-email',
            'contact[message]' => 'short'
        ]);

        $client->submit($form);

        self::assertResponseRedirects('/#contact');
        $client->followRedirect();
        self::assertSelectorTextContains('.alert-danger', 'Please check the form for errors.');
    }
}
