<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

readonly class FileUploader
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

        $ext = $file->guessExtension()
            ?? $file->getClientOriginalExtension()
            ?: 'bin';

        $fileName = $safeFilename . '-' . uniqid('', true) . '.' . $file->guessExtension();

        try {
            $file->move($this->targetDirectory, $fileName);
        } catch (\Throwable $e) {
            throw new FileException(sprintf(
                'Could not upload file to "%s": %s',
                $this->targetDirectory,
                $e->getMessage()
            ), 0, $e);
        }

        return $fileName;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }

    public function remove(?string $fileName): void
    {
        if (!$fileName) {
            return;
        }

        // Empêche toute tentative de traversal (../)
        $fileName = basename($fileName);

        $filePath = rtrim($this->targetDirectory, '/').'/'.$fileName;

        if (is_file($filePath)) {
            @unlink($filePath);
        }
    }

    private function ensureTargetDirectoryExists(): void
    {
        $dir = $this->targetDirectory;

        if (is_dir($dir)) {
            if (!is_writable($dir)) {
                throw new FileException(sprintf(
                    'Upload directory "%s" is not writable by the PHP process.',
                    $dir
                ));
            }

            return;
        }

        // 0775 est un bon défaut (umask peut encore influencer selon l’OS)
        $oldUmask = umask(0002);
        $ok = @mkdir($dir, 0775, true);
        umask($oldUmask);

        if (!$ok && !is_dir($dir)) {
            $err = error_get_last();
            $parentDir = dirname($dir);

            throw new FileException(sprintf(
                'Upload directory "%s" could not be created. Parent "%s" must be writable. %s',
                $dir,
                $parentDir,
                $err['message'] ?? ''
            ));
        }

        if (!is_writable($dir)) {
            throw new FileException(sprintf(
                'Upload directory "%s" was created but is not writable by the PHP process.',
                $dir
            ));
        }
    }
}
