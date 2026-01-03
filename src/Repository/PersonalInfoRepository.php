<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\PersonalInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PersonalInfo>
 */
final class PersonalInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonalInfo::class);
    }

    public function findOneOrNew(): PersonalInfo
    {
        return $this->findOneBy([]) ?? new PersonalInfo();
    }
}
