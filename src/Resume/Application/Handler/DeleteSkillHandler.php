<?php

declare(strict_types=1);

namespace App\Resume\Application\Handler;

use App\Resume\Application\Command\DeleteSkillCommand;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DeleteSkillHandler
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function __invoke(DeleteSkillCommand $command): void
    {
        $this->em->remove($command->skill);
        $this->em->flush();
    }
}
