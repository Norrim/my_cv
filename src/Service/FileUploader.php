<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

final readonly class FileUploader
{
    public function __construct(
        private string $targetDirectory,
        private SluggerInterface $slugger,
    ) {}

    public function upload(UploadedFile $file): string
    {
        $this->ensureTargetDirectoryExists();

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = (string) $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '-' . uniqid('', true) . '.' . $file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (\Throwable $e) {
            throw new FileException('Could not upload file: ' . $e->getMessage(), 0, $e);
        }

        return $fileName;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }

    private function ensureTargetDirectoryExists(): void
    {
        $dir = $this->getTargetDirectory();

        if (!is_dir($dir)) {
            // 0775 est un bon défaut (à adapter selon ton infra/umask)
            if (!@mkdir($dir, 0775, true) && !is_dir($dir)) {
                throw new FileException(sprintf('Upload directory "%s" could not be created.', $dir));
            }
        }

        if (!is_writable($dir)) {
            throw new FileException(sprintf('Upload directory "%s" is not writable.', $dir));
        }
    }
}
