<?php

declare(strict_types=1);

namespace App\Resume\Domain\Repository;

use App\Resume\Domain\Entity\Skill;

interface SkillRepositoryInterface
{
    /** @return Skill[] */
    public function findAllOrderedByPosition(): array;
}
