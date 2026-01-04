<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RecommendationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecommendationRepository::class)]
class Recommendation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $recommandedAt = null;

    #[ORM\Column(name: '`current_role`', length: 255, nullable: true)]
    private ?string $currentRole = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $roleAtThatTime = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column]
    private ?int $position = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getRecommandedAt(): ?\DateTimeInterface
    {
        return $this->recommandedAt;
    }

    public function setRecommandedAt(?\DateTimeInterface $recommandedAt): self
    {
        $this->recommandedAt = $recommandedAt;

        return $this;
    }

    public function getCurrentRole(): ?string
    {
        return $this->currentRole;
    }

    public function setCurrentRole(?string $currentRole): self
    {
        $this->currentRole = $currentRole;

        return $this;
    }

    public function getRoleAtThatTime(): ?string
    {
        return $this->roleAtThatTime;
    }

    public function setRoleAtThatTime(?string $roleAtThatTime): self
    {
        $this->roleAtThatTime = $roleAtThatTime;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getFullName(): string
    {
        return sprintf('%s %s', $this->firstname ?? '', $this->lastname ?? '');
    }
}
