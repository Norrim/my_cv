<?php

declare(strict_types=1);

namespace App\Resume\Application\Command;

use App\Resume\Domain\Entity\Experience;

final readonly class SaveExperienceCommand
{
    public function __construct(
        public Experience $experience,
    ) {
    }
}
