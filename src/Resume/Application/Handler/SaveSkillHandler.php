<?php

declare(strict_types=1);

namespace App\Resume\Application\Handler;

use App\Resume\Application\Command\SaveSkillCommand;
use Doctrine\ORM\EntityManagerInterface;

final readonly class SaveSkillHandler
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(SaveSkillCommand $command): void
    {
        $this->em->persist($command->skill);
        $this->em->flush();
    }
}
