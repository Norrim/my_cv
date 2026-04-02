<?php

declare(strict_types=1);

namespace App\Portfolio\Infrastructure\Doctrine;

use App\Portfolio\Domain\Entity\Recommendation;
use App\Portfolio\Domain\Repository\RecommendationRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recommendation>
 */
final class RecommendationRepository extends ServiceEntityRepository implements RecommendationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recommendation::class);
    }

    /** @return Recommendation[] */
    public function findAllOrderedByPosition(): array
    {
        return $this->findBy([], ['position' => 'ASC']);
    }
}
