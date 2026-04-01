<?php

declare(strict_types=1);

namespace App\Resume\Domain\Repository;

use App\Resume\Domain\Entity\Skill;

/**
 * @method Skill|null find($id, $lockMode = null, $lockVersion = null)
 * @method Skill|null findOneBy(array $criteria, array $orderBy = null)
 * @method Skill[]    findAll()
 * @method Skill[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface SkillRepositoryInterface
{
    /** @return Skill[] */
    public function findAllOrderedByPosition(): array;
}
