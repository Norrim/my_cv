<?php

declare(strict_types=1);

namespace App\Resume\Application\Command;

use App\Resume\Domain\Entity\Education;

final readonly class DeleteEducationCommand
{
    public function __construct(
        public Education $education,
    ) {
    }
}
