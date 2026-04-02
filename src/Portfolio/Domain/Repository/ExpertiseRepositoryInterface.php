<?php

declare(strict_types=1);

namespace App\Portfolio\Domain\Repository;

use App\Portfolio\Domain\Entity\Expertise;

interface ExpertiseRepositoryInterface
{
    /** @return Expertise[] */
    public function findAllOrderedByPosition(): array;

    /** @return Expertise[] */
    public function findAll(): array;
}
