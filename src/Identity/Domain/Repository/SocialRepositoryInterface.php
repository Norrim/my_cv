<?php

declare(strict_types=1);

namespace App\Identity\Domain\Repository;

use App\Identity\Domain\Entity\Social;

interface SocialRepositoryInterface
{
    /** @return Social[] */
    public function findAllOrderedByPosition(): array;
}
