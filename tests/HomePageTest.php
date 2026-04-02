<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Identity\Domain\Entity\PersonalInfo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class HomePageTest extends WebTestCase
{
    public function testHomepageIsDisplayed(): void
    {
        $client = self::createClient();
        $em = self::getContainer()->get(EntityManagerInterface::class);

        if (!$em->getRepository(PersonalInfo::class)->findOneBy([])) {
            $personalInfo = (new PersonalInfo())
                ->setFirstname('John')
                ->setName('Doe')
                ->setTitle('Developer')
                ->setEmail('john@example.com')
                ->setPhoneNumber('0600000000')
                ->setLocalisation('Paris, France');

            $em->persist($personalInfo);
            $em->flush();
        }

        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
    }
}
