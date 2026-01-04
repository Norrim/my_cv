<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\String\Slugger\SluggerInterface;

final readonly class ClientFileUploader extends FileUploader
{
    public function __construct(
        #[Autowire('%clients_logos_directory%')]
        string $targetDirectory,
        SluggerInterface $slugger,
    ) {
        parent::__construct($targetDirectory, $slugger);
    }
}
