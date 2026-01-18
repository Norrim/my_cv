<?php

declare(strict_types=1);

namespace App\Tests\Dto;

use App\Dto\ContactRequest;
use PHPUnit\Framework\TestCase;

final class ContactRequestTest extends TestCase
{
    public function testContactRequestProperties(): void
    {
        $contactRequest = new ContactRequest();
        $contactRequest->name = 'John Doe';
        $contactRequest->email = 'john@example.com';
        $contactRequest->message = 'Ceci est un message de test de plus de dix caractères.';

        $this->assertSame('John Doe', $contactRequest->name);
        $this->assertSame('john@example.com', $contactRequest->email);
        $this->assertSame('Ceci est un message de test de plus de dix caractères.', $contactRequest->message);
    }
}
