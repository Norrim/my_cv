<?php

declare(strict_types=1);

namespace App\Contact\Application\DTO\Step;

use Symfony\Component\Validator\Constraints as Assert;

final class ProjectDto
{
    #[Assert\NotBlank(groups: ['project'])]
    #[Assert\Length(min: 20, groups: ['project'])]
    public ?string $projectDescription = null;

    #[Assert\Length(max: 500, groups: ['project'])]
    public ?string $techStack = null;

    #[Assert\Length(max: 100, groups: ['project'])]
    public ?string $estimatedDuration = null;

    #[Assert\Length(max: 2000, groups: ['project'])]
    public ?string $freeMessage = null;
}
