<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Client;
use App\Entity\Users;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class ClientControllerTest extends AbstractControllerTest
{
    public function testEditAllClients(): void
    {
        $client = $this->createAdminClient();

        $this->setupDependencies();
        $crawler = $client->request('GET', '/client/edit-all');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Enregistrer')->form();

        $photoPath = tempnam(sys_get_temp_dir(), 'test_image') . '.png';
        file_put_contents($photoPath, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='));

        $logo = new UploadedFile(
            $photoPath,
            'logo.png',
            'image/png',
            null,
            true
        );

        $client->request('POST', '/client/edit-all', [
            'form' => [
                'clients' => [
                    [
                        'name' => 'Test Client',
                        'position' => 1,
                    ]
                ],
                '_token' => $form['form[_token]']->getValue(),
            ]
        ], [
            'form' => [
                'clients' => [
                    ['logo' => $logo]
                ]
            ]
        ]);

        $this->assertResponseRedirects('/');

        $dbClient = $this->em->getRepository(Client::class)->findOneBy(['name' => 'Test Client']);
        $this->assertNotNull($dbClient);
        $this->assertNotNull($dbClient->getUrl());
        $this->assertStringContainsString('uploads/images/clients/', $dbClient->getUrl());
    }
}
