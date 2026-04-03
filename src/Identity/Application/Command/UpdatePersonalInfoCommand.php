<?php

declare(strict_types=1);

namespace App\Identity\Application\Command;

use App\Identity\Domain\Entity\PersonalInfo;

final readonly class UpdatePersonalInfoCommand
{
    public function __construct(
        public PersonalInfo $personalInfo,
    ) {}
}
