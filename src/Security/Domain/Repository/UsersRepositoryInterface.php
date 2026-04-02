<?php

declare(strict_types=1);

namespace App\Security\Domain\Repository;

use App\Security\Domain\Entity\Users;

interface UsersRepositoryInterface
{
    public function findOneByEmail(string $email): ?Users;
}
