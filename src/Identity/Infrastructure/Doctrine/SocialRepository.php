<?php

declare(strict_types=1);

namespace App\Identity\Infrastructure\Doctrine;

use App\Identity\Domain\Entity\Social;
use App\Identity\Domain\Repository\SocialRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Social>
 */
final class SocialRepository extends ServiceEntityRepository implements SocialRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Social::class);
    }

    /** @return Social[] */
    public function findAllOrderedByPosition(): array
    {
        return $this->findBy([], ['position' => 'ASC']);
    }
}
