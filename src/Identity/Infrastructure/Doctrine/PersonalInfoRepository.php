<?php

declare(strict_types=1);

namespace App\Identity\Infrastructure\Doctrine;

use App\Identity\Domain\Entity\PersonalInfo;
use App\Identity\Domain\Repository\PersonalInfoRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PersonalInfo>
 */
final class PersonalInfoRepository extends ServiceEntityRepository implements PersonalInfoRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonalInfo::class);
    }

    public function findOneOrNew(): PersonalInfo
    {
        return $this->findOneBy([]) ?? new PersonalInfo();
    }

    public function findFirst(): ?PersonalInfo
    {
        return $this->findOneBy([]);
    }
}
