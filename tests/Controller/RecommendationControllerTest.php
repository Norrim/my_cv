<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Recommendation;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class RecommendationControllerTest extends AbstractControllerTest
{
    public function testEditAllRecommendations(): void
    {
        $client = $this->createAdminClient();

        $this->setupDependencies();
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
