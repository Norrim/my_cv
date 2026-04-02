<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Doctrine;

use App\Security\Domain\Entity\Users;
use App\Security\Domain\Repository\UsersRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Users>
 */
final class UsersRepository extends ServiceEntityRepository implements UsersRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Users::class);
    }

    public function findOneByEmail(string $email): ?Users
    {
        return $this->findOneBy(['email' => $email]);
    }
}
