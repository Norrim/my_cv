<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Expertise;
use App\Entity\Users;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ExpertiseControllerTest extends WebTestCase
{
    private EntityManagerInterface $em;
    private UsersRepository $userRepository;

    private function setupDependencies(): void
    {
        $this->em = static::getContainer()->get('doctrine')->getManager();
        $this->userRepository = $this->em->getRepository(Users::class);
    }

    private function getAdminUser(): Users
    {
        return $this->userRepository->findOneBy(['email' => 'test@example.com']);
    }

    public function testEditAllExpertises(): void
    {
        $client = static::createClient();
        $this->setupDependencies();
        $client->loginUser($this->getAdminUser());

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
