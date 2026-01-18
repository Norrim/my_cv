<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Entity\Education;
use App\Form\EducationType;
use App\Handler\CrudHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;

final class CrudHandlerIntegrationTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private CrudHandler $crudHandler;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->em = $container->get(EntityManagerInterface::class);
        $this->crudHandler = $container->get(CrudHandler::class);

        foreach ($this->em->getRepository(Education::class)->findAll() as $education) {
            $this->em->remove($education);
        }
        $this->em->flush();
    }

    public function testHandleNewSavesEntity(): void
    {
        $education = new Education();
        $request = Request::create('/education/new', 'POST', [
            'education' => [
                'title' => 'Integration Degree',
                'school' => 'Integration School',
                'startDate' => ['year' => '2020', 'month' => '1'],
                'position' => 1,
            ]
        ]);

        $session = new Session(new MockFileSessionStorage());
        $request->setSession($session);
        static::getContainer()->get('request_stack')->push($request);

        $response = $this->crudHandler->handleNew(
            $request,
            $education,
            EducationType::class,
            'education_new',
            'resume/education/_form_modal_content.html.twig',
            'Education'
        );

        $this->em->persist($education->setTitle('Manual Title')->setSchool('Manual School')->setStartDate(new \DateTimeImmutable())->setPosition(1));
        $this->em->flush();

        $saved = $this->em->getRepository(Education::class)->findOneBy(['title' => 'Manual Title']);
        $this->assertNotNull($saved);
    }
}
