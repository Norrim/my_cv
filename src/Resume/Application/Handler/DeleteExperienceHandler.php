<?php

declare(strict_types=1);

namespace App\Resume\Application\Handler;

use App\Resume\Application\Command\DeleteExperienceCommand;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DeleteExperienceHandler
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function __invoke(DeleteExperienceCommand $command): void
    {
        $this->em->remove($command->experience);
        $this->em->flush();
    }
}
