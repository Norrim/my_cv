<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\PersonalInfo;

final class PersonalInfoControllerTest extends AbstractControllerTest
{
    public function testEditPersonalInfo(): void
    {
        $client = $this->createAdminClient();

        $this->setupDependencies();
        foreach ($this->em->getRepository(PersonalInfo::class)->findAll() as $pi) {
            $this->em->remove($pi);
        }
        $this->em->flush();

        $crawler = $client->request('GET', '/personal-info/edit');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Enregistrer')->form();
        $form['personal_info[firstname]'] = 'UniqueName';
        $form['personal_info[name]'] = 'Tiertant';
        $form['personal_info[title]'] = 'Fullstack Developer';
        $form['personal_info[email]'] = 'remi@example.com';

        $client->submit($form);

        $this->assertResponseRedirects('/');

        $this->em->clear();
        $info = $this->em->getRepository(PersonalInfo::class)->findOneBy([]);
        $this->assertSame('UniqueName', $info->getFirstname());
        $this->assertSame('Fullstack Developer', $info->getTitle());
    }
}
