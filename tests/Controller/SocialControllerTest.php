<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Social;
use App\Entity\Users;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SocialControllerTest extends WebTestCase
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

    public function testEditAllSocials(): void
    {
        $client = self::createClient();
        $this->setupDependencies();
        $client->loginUser($this->getAdminUser());

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
