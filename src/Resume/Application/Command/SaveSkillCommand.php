<?php

declare(strict_types=1);

namespace App\Resume\Application\Command;

use App\Resume\Domain\Entity\Skill;

final readonly class SaveSkillCommand
{
    public function __construct(
        public Skill $skill,
    ) {}
}
