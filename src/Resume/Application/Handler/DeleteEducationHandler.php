<?php

declare(strict_types=1);

namespace App\Resume\Application\Handler;

use App\Resume\Application\Command\DeleteEducationCommand;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DeleteEducationHandler
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function __invoke(DeleteEducationCommand $command): void
    {
        $this->em->remove($command->education);
        $this->em->flush();
    }
}
