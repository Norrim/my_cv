<?php

declare(strict_types=1);

namespace App\Portfolio\Domain\Repository;

use App\Portfolio\Domain\Entity\Recommendation;

interface RecommendationRepositoryInterface
{
    /** @return Recommendation[] */
    public function findAllOrderedByPosition(): array;

    /** @return Recommendation[] */
    public function findAll(): array;
}
