<?php

declare(strict_types=1);

namespace App\Resume\Application\Command;

use App\Resume\Domain\Entity\Experience;

final readonly class DeleteExperienceCommand
{
    public function __construct(
        public Experience $experience,
    ) {}
}
