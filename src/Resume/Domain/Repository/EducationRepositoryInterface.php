<?php

declare(strict_types=1);

namespace App\Resume\Domain\Repository;

use App\Resume\Domain\Entity\Education;

interface EducationRepositoryInterface
{
    /** @return Education[] */
    public function findAllOrderedByPosition(): array;
}
