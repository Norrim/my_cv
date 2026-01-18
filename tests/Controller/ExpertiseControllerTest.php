<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Expertise;

final class ExpertiseControllerTest extends AbstractControllerTest
{
    public function testEditAllExpertises(): void
    {
        $client = $this->createAdminClient();

        $this->setupDependencies();
        $crawler = $client->request('GET', '/expertise/edit-all');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Enregistrer')->form();
        $client->request('POST', '/expertise/edit-all', [
            'form' => [
                'expertises' => [
                    [
                        'title' => 'Backend Development',
                        'content' => 'Building robust APIs',
                        'position' => 1
                    ],
                    [
                        'title' => 'Frontend Development',
                        'content' => 'Responsive UI',
                        'position' => 2
                    ]
                ],
                '_token' => $form['form[_token]']->getValue(),
            ]
        ]);

        $this->assertResponseRedirects('/');

        $repo = $this->em->getRepository(Expertise::class);
        $this->assertCount(2, $repo->findAll());
        $this->assertNotNull($repo->findOneBy(['title' => 'Backend Development']));
    }
}
