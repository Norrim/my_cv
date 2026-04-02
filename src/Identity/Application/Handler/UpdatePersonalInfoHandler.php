<?php

declare(strict_types=1);

namespace App\Identity\Application\Handler;

use App\Identity\Application\Command\UpdatePersonalInfoCommand;
use Doctrine\ORM\EntityManagerInterface;

final readonly class UpdatePersonalInfoHandler
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(UpdatePersonalInfoCommand $command): void
    {
        $this->em->persist($command->personalInfo);
        $this->em->flush();
    }
}
