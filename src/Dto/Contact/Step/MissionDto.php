<?php

declare(strict_types=1);

namespace App\Dto\Contact\Step;

use Symfony\Component\Validator\Constraints as Assert;

final class MissionDto
{
    #[Assert\NotBlank(groups: ['mission'])]
    #[Assert\Choice(choices: ['freelance', 'cdi', 'short_mission'], groups: ['mission'])]
    public ?string $contractType = null;

    #[Assert\NotBlank(groups: ['mission'])]
    #[Assert\Choice(choices: ['remote', 'hybrid', 'onsite'], groups: ['mission'])]
    public ?string $workMode = null;

    #[Assert\Length(max: 200, groups: ['mission'])]
    public ?string $location = null;

    #[Assert\Length(max: 50, groups: ['mission'])]
    public ?string $dailyRate = null;

    public ?\DateTimeImmutable $startDate = null;
}
