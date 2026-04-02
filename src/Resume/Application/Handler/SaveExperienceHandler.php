<?php

declare(strict_types=1);

namespace App\Resume\Application\Handler;

use App\Resume\Application\Command\SaveExperienceCommand;
use Doctrine\ORM\EntityManagerInterface;

final readonly class SaveExperienceHandler
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(SaveExperienceCommand $command): void
    {
        $this->em->persist($command->experience);
        $this->em->flush();
    }
}
