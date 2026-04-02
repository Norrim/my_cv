<?php

declare(strict_types=1);

namespace App\Portfolio\Domain\Repository;

use App\Portfolio\Domain\Entity\Client;

interface ClientRepositoryInterface
{
    /** @return Client[] */
    public function findAllOrderedByPosition(): array;

    /** @return Client[] */
    public function findAll(): array;
}
