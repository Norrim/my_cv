<?php

declare(strict_types=1);

namespace App\Identity\Domain\Repository;

use App\Identity\Domain\Entity\PersonalInfo;

interface PersonalInfoRepositoryInterface
{
    public function findOneOrNew(): PersonalInfo;

    public function findFirst(): ?PersonalInfo;
}
