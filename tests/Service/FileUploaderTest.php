<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\FileUploader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\UnicodeString;

final class FileUploaderTest extends TestCase
{
    public function testUpload(): void
    {
        $targetDirectory = '/tmp/uploads';
        $slugger = $this->createMock(SluggerInterface::class);
        $slugger->method('slug')->willReturn(new UnicodeString('test-file'));

        $file = $this->createMock(UploadedFile::class);
        $file->method('getClientOriginalName')->willReturn('test file.jpg');
        $file->method('guessExtension')->willReturn('jpg');

        $file->expects($this->once())
            ->method('move')
            ->with($this->equalTo($targetDirectory), $this->isString());

        $uploader = new FileUploader($targetDirectory, $slugger);

        $fileName = $uploader->upload($file);

        $this->assertStringContainsString('test-file-', $fileName);
        $this->assertStringEndsWith('.jpg', $fileName);
    }

    public function testGetTargetDirectory(): void
    {
        $targetDirectory = '/tmp/uploads';
        $slugger = $this->createMock(SluggerInterface::class);
        $uploader = new FileUploader($targetDirectory, $slugger);

        $this->assertSame($targetDirectory, $uploader->getTargetDirectory());
    }
}
