<?php

declare(strict_types=1);

namespace App\Portfolio\Infrastructure\Doctrine;

use App\Portfolio\Domain\Entity\Expertise;
use App\Portfolio\Domain\Repository\ExpertiseRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Expertise>
 */
final class ExpertiseRepository extends ServiceEntityRepository implements ExpertiseRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expertise::class);
    }

    /** @return Expertise[] */
    public function findAllOrderedByPosition(): array
    {
        return $this->findBy([], ['position' => 'ASC']);
    }
}
