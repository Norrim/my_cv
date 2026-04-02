<?php

declare(strict_types=1);

namespace App\Contact\Application\DTO;

use App\Contact\Application\DTO\Step\IdentityDto;
use App\Contact\Application\DTO\Step\MissionDto;
use App\Contact\Application\DTO\Step\ProjectDto;
use Symfony\Component\Validator\Constraints as Assert;

final class ContactDataDto
{
    public string $currentStep = 'identity';

    #[Assert\Valid(groups: ['identity'])]
    public IdentityDto $identity;

    #[Assert\Valid(groups: ['mission'])]
    public MissionDto $mission;

    #[Assert\Valid(groups: ['project'])]
    public ProjectDto $project;

    public function __construct()
    {
        $this->identity = new IdentityDto();
        $this->mission = new MissionDto();
        $this->project = new ProjectDto();
    }
}
