<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Social;

final class SocialControllerTest extends AbstractControllerTest
{
    public function testEditAllSocials(): void
    {
        $client = $this->createAdminClient();

        $this->setupDependencies();
        $crawler = $client->request('GET', '/social/edit-all');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Enregistrer')->form();
        $client->request('POST', '/social/edit-all', [
            'form' => [
                'socials' => [
                    [
                        'name' => 'GitHub',
                        'url' => 'https://github.com/rtiertant',
                        'iconClass' => 'feathericon-github',
                        'position' => 1
                    ]
                ],
                '_token' => $form['form[_token]']->getValue(),
            ]
        ]);

        $this->assertResponseRedirects('/#resume');

        $social = $this->em->getRepository(Social::class)->findOneBy(['name' => 'GitHub']);
        $this->assertNotNull($social);
    }
}
