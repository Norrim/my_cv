<?php

declare(strict_types=1);

namespace App\Portfolio\Application\Command;

use App\Portfolio\Domain\Entity\Recommendation;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final readonly class UpdateRecommendationsCommand
{
    /**
     * @param Recommendation[] $submittedRecommendations
     * @param array<int, UploadedFile|null> $imageFiles
     */
    public function __construct(
        public array $submittedRecommendations,
        public array $imageFiles = [],
    ) {
    }
}
