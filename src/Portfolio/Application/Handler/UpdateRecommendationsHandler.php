<?php

declare(strict_types=1);

namespace App\Portfolio\Application\Handler;

use App\Portfolio\Application\Command\UpdateRecommendationsCommand;
use App\Portfolio\Domain\Repository\RecommendationRepositoryInterface;
use App\Portfolio\Infrastructure\Storage\RecommendationFileUploader;
use Doctrine\ORM\EntityManagerInterface;

use function in_array;

final readonly class UpdateRecommendationsHandler
{
    public function __construct(
        private RecommendationRepositoryInterface $recommendationRepository,
        private EntityManagerInterface $em,
        private RecommendationFileUploader $fileUploader,
    ) {}

    public function __invoke(UpdateRecommendationsCommand $command): void
    {
        $existingRecommendations = $this->recommendationRepository->findAll();

        foreach ($existingRecommendations as $existingRecommendation) {
            if (!in_array($existingRecommendation, $command->submittedRecommendations, true)) {
                if ($existingRecommendation->hasImage()) {
                    $this->fileUploader->remove($existingRecommendation->getImageBasename());
                }
                $this->em->remove($existingRecommendation);
            }
        }

        foreach ($command->submittedRecommendations as $key => $recommendation) {
            $imageFile = $command->imageFiles[$key] ?? null;

            if ($imageFile) {
                if ($recommendation->hasImage()) {
                    $this->fileUploader->remove($recommendation->getImageBasename());
                }

                $imageFileName = $this->fileUploader->upload($imageFile);
                $recommendation->updateImageUrl('uploads/images/recommendations/' . $imageFileName);
            }

            $this->em->persist($recommendation);
        }

        $this->em->flush();
    }
}
