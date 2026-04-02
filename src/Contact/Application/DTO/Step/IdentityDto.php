<?php

declare(strict_types=1);

namespace App\Contact\Application\DTO\Step;

use Symfony\Component\Validator\Constraints as Assert;

final class IdentityDto
{
    #[Assert\NotBlank(groups: ['identity'])]
    #[Assert\Length(min: 2, max: 100, groups: ['identity'])]
    public ?string $lastName = null;

    #[Assert\NotBlank(groups: ['identity'])]
    #[Assert\Length(min: 2, max: 100, groups: ['identity'])]
    public ?string $firstName = null;

    #[Assert\NotBlank(groups: ['identity'])]
    #[Assert\Email(groups: ['identity'])]
    public ?string $email = null;

    #[Assert\Length(max: 100, groups: ['identity'])]
    public ?string $company = null;

    #[Assert\Length(max: 20, groups: ['identity'])]
    public ?string $phone = null;
}
