<?php

declare(strict_types=1);

namespace App\Resume\Infrastructure\Doctrine;

use App\Resume\Domain\Entity\Education;
use App\Resume\Domain\Repository\EducationRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Education>
 */
final class EducationRepository extends ServiceEntityRepository implements EducationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Education::class);
    }

    /** @return Education[] */
    public function findAllOrderedByPosition(): array
    {
        return $this->findBy([], ['position' => 'ASC']);
    }
}
