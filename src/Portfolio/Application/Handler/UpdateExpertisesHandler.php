<?php

declare(strict_types=1);

namespace App\Portfolio\Application\Handler;

use App\Portfolio\Application\Command\UpdateExpertisesCommand;
use App\Portfolio\Domain\Repository\ExpertiseRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

use function in_array;

final readonly class UpdateExpertisesHandler
{
    public function __construct(
        private ExpertiseRepositoryInterface $expertiseRepository,
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(UpdateExpertisesCommand $command): void
    {
        $existingExpertises = $this->expertiseRepository->findAll();

        foreach ($existingExpertises as $existingExpertise) {
            if (!in_array($existingExpertise, $command->submittedExpertises, true)) {
                $this->em->remove($existingExpertise);
            }
        }

        foreach ($command->submittedExpertises as $expertise) {
            $this->em->persist($expertise);
        }

        $this->em->flush();
    }
}
