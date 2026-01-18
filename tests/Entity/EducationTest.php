<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Education;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class EducationTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $education = new Education();
        $title = 'Master Informatique';
        $school = 'Université de Paris';
        $startDate = new DateTimeImmutable('2020-09-01');
        $endDate = new DateTimeImmutable('2022-06-30');
        $description = 'Formation approfondie';
        $position = 2;

        $education->setTitle($title);
        $education->setSchool($school);
        $education->setStartDate($startDate);
        $education->setEndDate($endDate);
        $education->setDescription($description);
        $education->setPosition($position);

        $this->assertSame($title, $education->getTitle());
        $this->assertSame($school, $education->getSchool());
        $this->assertSame($startDate, $education->getStartDate());
        $this->assertSame($endDate, $education->getEndDate());
        $this->assertSame($description, $education->getDescription());
        $this->assertSame($position, $education->getPosition());
    }

    public function testValidateDatesWithInvalidRange(): void
    {
        $education = new Education();
        $education->setStartDate(new DateTimeImmutable('2022-01-01'));
        $education->setEndDate(new DateTimeImmutable('2021-01-01'));

        $context = $this->createMock(ExecutionContextInterface::class);
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $context->expects($this->once())
            ->method('buildViolation')
            ->with('La date de fin ne peut pas être antérieure à la date de début.')
            ->willReturn($violationBuilder);

        $violationBuilder->expects($this->once())
            ->method('atPath')
            ->with('endDate')
            ->willReturn($violationBuilder);

        $violationBuilder->expects($this->once())
            ->method('addViolation');

        $education->validateDates($context);
    }

    public function testValidateDatesWithValidRange(): void
    {
        $education = new Education();
        $education->setStartDate(new DateTimeImmutable('2021-01-01'));
        $education->setEndDate(new DateTimeImmutable('2022-01-01'));

        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->never())->method('buildViolation');

        $education->validateDates($context);
    }
}
