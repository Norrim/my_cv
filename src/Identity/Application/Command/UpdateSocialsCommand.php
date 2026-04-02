<?php

declare(strict_types=1);

namespace App\Identity\Application\Command;

use App\Identity\Domain\Entity\Social;

final readonly class UpdateSocialsCommand
{
    /** @param Social[] $submittedSocials */
    public function __construct(
        public array $submittedSocials,
    ) {
    }
}
