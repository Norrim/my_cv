<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class SecurityControllerTest extends WebTestCase
{
    public function testLogin(): void
    {
        $client = static::createClient();
        $container = static::getContainer();
        $em = $container->get(EntityManagerInterface::class);
        $hasher = $container->get(UserPasswordHasherInterface::class);

        $repo = $em->getRepository(Users::class);
        $existing = $repo->findOneBy(['email' => 'test@example.com']);
        if ($existing) {
            $em->remove($existing);
            $em->flush();
        }

        $user = (new Users())
            ->setEmail('test@example.com')
            ->setRoles(['ROLE_ADMIN']);
        $user->setPassword($hasher->hashPassword($user, 'password'));

        $em->persist($user);
        $em->flush();

        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Connexion')->form();
        $form['_username'] = 'test@example.com';
        $form['_password'] = 'password';

        $client->submit($form);

        $this->assertResponseRedirects('/admin');
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testLoginInvalidCredentials(): void
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Connexion')->form();
        $form['_username'] = 'wrong@example.com';
        $form['_password'] = 'wrong';

        $client->submit($form);

        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertSelectorExists('.alert-danger');
    }
}
