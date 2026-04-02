<?php

declare(strict_types=1);

namespace App\Portfolio\Infrastructure\Storage;

use App\Shared\Infrastructure\Service\FileUploader;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\String\Slugger\SluggerInterface;

readonly class ClientFileUploader extends FileUploader
{
    public function __construct(
        #[Autowire('%clients_logos_directory%')]
        string $targetDirectory,
        SluggerInterface $slugger,
    ) {
        parent::__construct($targetDirectory, $slugger);
    }
}
