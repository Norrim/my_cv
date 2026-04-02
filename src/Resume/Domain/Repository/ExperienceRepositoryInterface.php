<?php

declare(strict_types=1);

namespace App\Resume\Domain\Repository;

use App\Resume\Domain\Entity\Experience;

interface ExperienceRepositoryInterface
{
    /** @return Experience[] */
    public function findAllOrderedByPosition(): array;
}
