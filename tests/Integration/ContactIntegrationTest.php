<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Dto\ContactRequest;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class ContactIntegrationTest extends KernelTestCase
{
    public function testMailerIntegration(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $mailer = $container->get(MailerInterface::class);

        $email = (new Email())
            ->from('test@example.com')
            ->to('contact@example.com')
            ->subject('Test Subject')
            ->text('Test Body');

        $mailer->send($email);

        $this->assertTrue(true);
    }
}
