<?php

declare(strict_types=1);

namespace App\Dto\Contact;

use App\Dto\Contact\Step\IdentityDto;
use App\Dto\Contact\Step\MissionDto;
use App\Dto\Contact\Step\ProjectDto;
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
