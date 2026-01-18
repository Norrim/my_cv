<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Recommendation;
use App\Entity\Users;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class RecommendationControllerTest extends WebTestCase
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

    public function testEditAllRecommendations(): void
    {
        $client = self::createClient();
        $this->setupDependencies();
        $client->loginUser($this->getAdminUser());

        $crawler = $client->request('GET', '/recommendation/edit-all');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Enregistrer')->form();
        $photoPath = tempnam(sys_get_temp_dir(), 'test_image') . '.png';
        file_put_contents($photoPath, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='));

        $image = new UploadedFile(
            $photoPath,
            'image.png',
            'image/png',
            null,
            true
        );

        $client->request('POST', '/recommendation/edit-all', [
            'form' => [
                'recommendations' => [
                    [
                        'firstname' => 'Jane',
                        'lastname' => 'Smith',
                        'currentRole' => 'CTO',
                        'content' => 'Exceptional skills.',
                        'position' => 1
                    ]
                ],
                '_token' => $form['form[_token]']->getValue(),
            ]
        ], [
            'form' => [
                'recommendations' => [
                    ['image' => $image]
                ]
            ]
        ]);

        $this->assertResponseRedirects('/');

        $recommendation = $this->em->getRepository(Recommendation::class)->findOneBy(['firstname' => 'Jane']);
        $this->assertNotNull($recommendation);
        $this->assertNotNull($recommendation->getImageUrl());
    }
}
