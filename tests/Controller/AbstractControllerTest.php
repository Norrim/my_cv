<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

abstract class AbstractControllerTest extends WebTestCase
{
    protected EntityManagerInterface $em;

    protected function createAdminClient(): KernelBrowser
    {
        $client = static::createClient();
        $this->em = self::getContainer()->get(EntityManagerInterface::class);

        $user = $this->ensureAdminUser();
        $client->loginUser($user);

        return $client;
    }

    protected function setupDependencies(): void
    {
        if (null === static::$kernel) {
            static::bootKernel();
        }
        $this->em = self::getContainer()->get(EntityManagerInterface::class);
    }

    protected function ensureAdminUser(): Users
    {
        $repo = $this->em->getRepository(Users::class);
        $user = $repo->findOneBy(['email' => 'test@example.com']);

        if (!$user) {
            $hasher = self::getContainer()->get(UserPasswordHasherInterface::class);
            $user = (new Users())
                ->setEmail('test@example.com')
                ->setRoles(['ROLE_ADMIN']);
            $user->setPassword($hasher->hashPassword($user, 'password'));

            $this->em->persist($user);
            $this->em->flush();
        }

        return $user;
    }
}
