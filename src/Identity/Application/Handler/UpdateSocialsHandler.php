<?php

declare(strict_types=1);

namespace App\Identity\Application\Handler;

use App\Identity\Application\Command\UpdateSocialsCommand;
use App\Identity\Domain\Repository\SocialRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

use function in_array;

final readonly class UpdateSocialsHandler
{
    public function __construct(
        private SocialRepositoryInterface $socialRepository,
        private EntityManagerInterface $em,
    ) {}

    public function __invoke(UpdateSocialsCommand $command): void
    {
        $existingSocials = $this->socialRepository->findAll();

        foreach ($existingSocials as $existingSocial) {
            if (!in_array($existingSocial, $command->submittedSocials, true)) {
                $this->em->remove($existingSocial);
            }
        }

        foreach ($command->submittedSocials as $social) {
            $this->em->persist($social);
        }

        $this->em->flush();
    }
}
