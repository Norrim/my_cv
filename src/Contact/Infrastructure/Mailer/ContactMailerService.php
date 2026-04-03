<?php

declare(strict_types=1);

namespace App\Contact\Infrastructure\Mailer;

use App\Contact\Application\DTO\ContactDataDto;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

final class ContactMailerService
{
    private const CONTRACT_TYPE_LABELS = [
        'freelance' => 'Freelance',
        'cdi' => 'CDI',
        'short_mission' => 'Mission courte',
    ];

    private const WORK_MODE_LABELS = [
        'remote' => 'Remote',
        'hybrid' => 'Hybride',
        'onsite' => 'Présentiel',
    ];

    public function __construct(
        private readonly MailerInterface $mailer,
        #[Autowire('%env(CONTACT_EMAIL)%')]
        private readonly string $contactEmail,
        #[Autowire('%env(CONTACT_FROM_EMAIL)%')]
        private readonly string $fromEmail,
    ) {}

    public function send(ContactDataDto $data): void
    {
        $identity = $data->identity;
        $mission = $data->mission;
        $project = $data->project;

        $contractLabel = self::CONTRACT_TYPE_LABELS[$mission->contractType ?? ''] ?? 'N/A';
        $workModeLabel = self::WORK_MODE_LABELS[$mission->workMode ?? ''] ?? 'N/A';

        $body = implode("\r\n", [
            '=== INFORMATIONS DU CONTACT ===',
            'Nom : ' . $identity->firstName . ' ' . $identity->lastName,
            'Email : ' . $identity->email,
            'Société : ' . ($identity->company ?: 'N/A'),
            'Téléphone : ' . ($identity->phone ?: 'N/A'),
            '',
            '=== DÉTAILS DE LA MISSION ===',
            'Type de contrat : ' . $contractLabel,
            'Modalité : ' . $workModeLabel,
            'Localisation : ' . ($mission->location ?: 'N/A'),
            'TJM proposé : ' . ($mission->dailyRate ?: 'N/A'),
            'Date de début : ' . ($mission->startDate?->format('d/m/Y') ?? 'N/A'),
            '',
            '=== DESCRIPTION DU PROJET ===',
            'Description : ' . ($project->projectDescription ?: 'N/A'),
            '',
            'Stack technique : ' . ($project->techStack ?: 'N/A'),
            '',
            'Durée estimée : ' . ($project->estimatedDuration ?: 'N/A'),
            '',
            'Message libre : ' . ($project->freeMessage ?: 'N/A'),
        ]);

        $email = new Email()
            ->from(new Address($this->fromEmail))
            ->to($this->contactEmail)
            ->replyTo(new Address((string) $identity->email, $identity->firstName . ' ' . $identity->lastName))
            ->subject(sprintf('[Site] %s %s — %s', $identity->firstName, $identity->lastName, $contractLabel))
            ->text($body);

        $this->mailer->send($email);
    }
}
