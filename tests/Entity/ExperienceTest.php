<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Experience;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class ExperienceTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $experience = new Experience();
        $title = 'Développeur PHP';
        $company = 'Ma Super Entreprise';
        $startDate = new DateTimeImmutable('2023-01-01');
        $endDate = new DateTimeImmutable('2024-01-01');
        $description = 'Description du poste';
        $position = 1;

        $experience->setTitle($title);
        $experience->setCompany($company);
        $experience->setStartDate($startDate);
        $experience->setEndDate($endDate);
        $experience->setDescription($description);
        $experience->setPosition($position);

        $this->assertSame($title, $experience->getTitle());
        $this->assertSame($company, $experience->getCompany());
        $this->assertSame($startDate, $experience->getStartDate());
        $this->assertSame($endDate, $experience->getEndDate());
        $this->assertSame($description, $experience->getDescription());
        $this->assertSame($position, $experience->getPosition());
    }
}
