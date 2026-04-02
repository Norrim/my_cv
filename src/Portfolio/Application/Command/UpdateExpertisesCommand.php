<?php

declare(strict_types=1);

namespace App\Portfolio\Application\Command;

use App\Portfolio\Domain\Entity\Expertise;

final readonly class UpdateExpertisesCommand
{
    /** @param Expertise[] $submittedExpertises */
    public function __construct(
        public array $submittedExpertises,
    ) {
    }
}
