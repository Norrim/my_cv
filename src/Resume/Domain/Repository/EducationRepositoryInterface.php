<?php

declare(strict_types=1);

namespace App\Resume\Domain\Repository;

use App\Resume\Domain\Entity\Education;

/**
 * @method Education|null find($id, $lockMode = null, $lockVersion = null)
 * @method Education|null findOneBy(array $criteria, array $orderBy = null)
 * @method Education[]    findAll()
 * @method Education[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface EducationRepositoryInterface
{
    /** @return Education[] */
    public function findAllOrderedByPosition(): array;
}
