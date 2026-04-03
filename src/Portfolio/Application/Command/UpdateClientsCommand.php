<?php

declare(strict_types=1);

namespace App\Portfolio\Application\Command;

use App\Portfolio\Domain\Entity\Client;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final readonly class UpdateClientsCommand
{
    /**
     * @param Client[] $submittedClients
     * @param array<int, UploadedFile|null> $logoFiles
     */
    public function __construct(
        public array $submittedClients,
        public array $logoFiles = [],
    ) {}
}
