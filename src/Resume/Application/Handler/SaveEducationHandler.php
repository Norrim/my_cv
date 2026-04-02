<?php

declare(strict_types=1);

namespace App\Resume\Application\Handler;

use App\Resume\Application\Command\SaveEducationCommand;
use Doctrine\ORM\EntityManagerInterface;

final readonly class SaveEducationHandler
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(SaveEducationCommand $command): void
    {
        $this->em->persist($command->education);
        $this->em->flush();
    }
}
