<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\PersonalInfo;
use App\Entity\Users;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PersonalInfoControllerTest extends WebTestCase
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

    public function testEditPersonalInfo(): void
    {
        $client = self::createClient();
        $this->setupDependencies();
        $client->loginUser($this->getAdminUser());

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
