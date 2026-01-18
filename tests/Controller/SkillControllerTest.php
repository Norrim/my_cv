<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Skill;

final class SkillControllerTest extends AbstractControllerTest
{
    public function testNewSkillRequiresLogin(): void
    {
        $client = self::createClient();
        $client->request('GET', '/skill/new');

        $this->assertResponseRedirects('/login');
    }

    public function testCreateSkill(): void
    {
        $client = $this->createAdminClient();

        $this->setupDependencies();
        $crawler = $client->request('GET', '/skill/new');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Enregistrer')->form();
        $form['skill[name]'] = 'PHP Integration Test';
        $form['skill[percentage]'] = 95;
        $form['skill[position]'] = 1;

        $client->submit($form);

        $this->assertResponseRedirects('/#resume');

        $skill = $this->em->getRepository(Skill::class)->findOneBy(['name' => 'PHP Integration Test']);
        $this->assertNotNull($skill);
    }

    public function testEditSkill(): void
    {
        $client = $this->createAdminClient();

        $this->setupDependencies();
        $skill = new Skill();
        $skill->setName('To be edited');
        $skill->setPercentage(50);
        $skill->setPosition(10);
        $this->em->persist($skill);
        $this->em->flush();

        $crawler = $client->request('GET', '/skill/' . $skill->getId() . '/edit');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Enregistrer')->form();
        $form['skill[name]'] = 'Edited Skill';
        $client->submit($form);

        $this->assertResponseRedirects('/#resume');

        $skill = $this->em->getRepository(Skill::class)->find($skill->getId());
        $this->assertSame('Edited Skill', $skill->getName());
    }

    public function testDeleteSkill(): void
    {
        $client = $this->createAdminClient();

        $this->setupDependencies();
        $skill = new Skill();
        $skill->setName('To be deleted');
        $skill->setPercentage(50);
        $skill->setPosition(11);
        $this->em->persist($skill);
        $this->em->flush();

        $id = $skill->getId();

        $crawler = $client->request('GET', '/');
        $form = $crawler->filter('form[action="/skill/' . $id . '/delete"]')->form();

        $client->submit($form);

        $this->assertResponseRedirects('/#resume');

        $this->em->clear();
        $deletedSkill = $this->em->getRepository(Skill::class)->find($id);
        $this->assertNull($deletedSkill);
    }
}
