<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Client;
use App\Entity\Expertise;
use App\Entity\Recommendation;
use App\Entity\Social;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class EntitiesTest extends TestCase
{
    public function testClient(): void
    {
        $client = new Client();
        $client->setName('Client A')->setUrl('https://logo.com')->setPosition(1);

        $this->assertSame('Client A', $client->getName());
        $this->assertSame('https://logo.com', $client->getUrl());
        $this->assertSame(1, $client->getPosition());
    }

    public function testExpertise(): void
    {
        $expertise = new Expertise();
        $expertise->setTitle('PHP')->setContent('Expert PHP 8')->setPosition(5);

        $this->assertSame('PHP', $expertise->getTitle());
        $this->assertSame('Expert PHP 8', $expertise->getContent());
        $this->assertSame(5, $expertise->getPosition());
    }

    public function testSocial(): void
    {
        $social = new Social();
        $social->setName('LinkedIn')->setUrl('https://linkedin.com')->setIconClass('fa-linkedin')->setPosition(2);

        $this->assertSame('LinkedIn', $social->getName());
        $this->assertSame('https://linkedin.com', $social->getUrl());
        $this->assertSame('fa-linkedin', $social->getIconClass());
        $this->assertSame(2, $social->getPosition());
    }

    public function testRecommendation(): void
    {
        $rec = new Recommendation();
        $date = new DateTimeImmutable();
        $rec->setFirstname('John')
            ->setLastname('Doe')
            ->setContent('Great worker')
            ->setRecommandedAt($date)
            ->setCurrentRole('CTO')
            ->setRoleAtThatTime('Lead Dev')
            ->setPosition(3);

        $this->assertSame('John', $rec->getFirstname());
        $this->assertSame('Doe', $rec->getLastname());
        $this->assertSame('John Doe', $rec->getFullName());
        $this->assertSame('Great worker', $rec->getContent());
        $this->assertSame($date, $rec->getRecommandedAt());
        $this->assertSame('CTO', $rec->getCurrentRole());
        $this->assertSame('Lead Dev', $rec->getRoleAtThatTime());
        $this->assertSame(3, $rec->getPosition());
    }
}
