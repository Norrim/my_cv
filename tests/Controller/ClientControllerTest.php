<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Client;
use App\Entity\Users;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class ClientControllerTest extends WebTestCase
{
    private EntityManagerInterface $em;
    private UsersRepository $userRepository;

    private function setupDependencies(): void
    {
        $this->em = self::getContainer()->get('doctrine')->getManager();
        $this->userRepository = $this->em->getRepository(Users::class);
    }

    private function getAdminUser(): Users
    {
        return $this->userRepository->findOneBy(['email' => 'test@example.com']);
    }

    public function testEditAllClients(): void
    {
        $client = self::createClient();
        $this->setupDependencies();
        $client->loginUser($this->getAdminUser());

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
