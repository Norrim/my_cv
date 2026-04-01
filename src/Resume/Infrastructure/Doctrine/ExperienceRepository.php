<?php

declare(strict_types=1);

namespace App\Resume\Infrastructure\Doctrine;

use App\Resume\Domain\Entity\Experience;
use App\Resume\Domain\Repository\ExperienceRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Experience>
 */
final class ExperienceRepository extends ServiceEntityRepository implements ExperienceRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Experience::class);
    }

    /** @return Experience[] */
    public function findAllOrderedByPosition(): array
    {
        return $this->findBy([], ['position' => 'ASC']);
    }
}
