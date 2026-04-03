<?php

declare(strict_types=1);

namespace App\Portfolio\Application\Handler;

use App\Portfolio\Application\Command\UpdateClientsCommand;
use App\Portfolio\Domain\Repository\ClientRepositoryInterface;
use App\Portfolio\Infrastructure\Storage\ClientFileUploader;
use Doctrine\ORM\EntityManagerInterface;

use function in_array;

final readonly class UpdateClientsHandler
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository,
        private EntityManagerInterface $em,
        private ClientFileUploader $fileUploader,
    ) {}

    public function __invoke(UpdateClientsCommand $command): void
    {
        $existingClients = $this->clientRepository->findAll();

        foreach ($existingClients as $existingClient) {
            if (!in_array($existingClient, $command->submittedClients, true)) {
                if ($existingClient->hasLogo()) {
                    $this->fileUploader->remove($existingClient->getLogoBasename());
                }
                $this->em->remove($existingClient);
            }
        }

        foreach ($command->submittedClients as $key => $client) {
            $logoFile = $command->logoFiles[$key] ?? null;

            if ($logoFile) {
                if ($client->hasLogo()) {
                    $this->fileUploader->remove($client->getLogoBasename());
                }

                $logoFileName = $this->fileUploader->upload($logoFile);
                $client->updateLogoUrl('uploads/images/clients/' . $logoFileName);
            }

            $this->em->persist($client);
        }

        $this->em->flush();
    }
}
