<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Education;
use App\Entity\Experience;
use App\Entity\Expertise;
use App\Entity\Recommendation;
use App\Entity\Social;
use App\Entity\PersonalInfo;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class ResumeCrudControllerTest extends AbstractControllerTest
{
    public function testEducationCrud(): void
    {
        $client = $this->createAdminClient();

        $this->setupDependencies();
        // Create
        $crawler = $client->request('GET', '/education/new');
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Enregistrer')->form();
        $form['education[title]'] = 'Test Degree';
        $form['education[school]'] = 'Test University';
        $form['education[startDate]'] = '2020-01-01';
        $form['education[position]'] = 1;
        $client->submit($form);
        $this->assertResponseRedirects('/#resume');

        /** @var Education $education */
        $education = $this->em->getRepository(Education::class)->findOneBy(['title' => 'Test Degree']);
        $this->assertNotNull($education);

        // Edit
        $crawler = $client->request('GET', '/education/' . $education->getId() . '/edit');
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Enregistrer')->form();
        $form['education[title]'] = 'Updated Degree';
        $client->submit($form);
        $this->assertResponseRedirects('/#resume');

        $this->em->refresh($education);
        $this->assertSame('Updated Degree', $education->getTitle());

        // Delete
        $crawler = $client->request('GET', '/');
        $form = $crawler->filter('form[action="/education/' . $education->getId() . '/delete"]')->form();
        $client->submit($form);
        $this->assertResponseRedirects('/#resume');

        $this->em->clear();
        $deletedEducation = $this->em->getRepository(Education::class)->find($education->getId());
        $this->assertNull($deletedEducation);
    }

    public function testExperienceCrud(): void
    {
        $client = $this->createAdminClient();

        $this->setupDependencies();
        // Create
        $crawler = $client->request('GET', '/experience/new');
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Enregistrer')->form();
        $form['experience[title]'] = 'Test Job';
        $form['experience[company]'] = 'Test Company';
        $form['experience[startDate]'] = '2021-01-01';
        $form['experience[position]'] = 1;
        $client->submit($form);
        $this->assertResponseRedirects('/#resume');

        $experience = $this->em->getRepository(Experience::class)->findOneBy(['title' => 'Test Job']);
        $this->assertNotNull($experience);

        // Delete
        $crawler = $client->request('GET', '/');
        $form = $crawler->filter('form[action="/experience/' . $experience->getId() . '/delete"]')->form();
        $client->submit($form);
        $this->assertResponseRedirects('/#resume');

        $this->em->clear();
        $deletedExperience = $this->em->getRepository(Experience::class)->find($experience->getId());
        $this->assertNull($deletedExperience);
    }

    public function testExpertiseEditAll(): void
    {
        $client = $this->createAdminClient();

        $this->setupDependencies();
        $crawler = $client->request('GET', '/expertise/edit-all');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Enregistrer')->form();
        $client->request('POST', '/expertise/edit-all', [
            'form' => [
                'expertises' => [
                    ['title' => 'Test Expertise', 'content' => 'Expertise content', 'position' => 1]
                ],
                '_token' => $form['form[_token]']->getValue(),
            ]
        ]);

        $this->assertResponseRedirects('/');

        $expertise = $this->em->getRepository(Expertise::class)->findOneBy(['title' => 'Test Expertise']);
        $this->assertNotNull($expertise);

        // Test delete via EditAll (sending empty list)
        $client->request('POST', '/expertise/edit-all', [
            'form' => [
                'expertises' => [],
                '_token' => $form['form[_token]']->getValue(),
            ]
        ]);
        $this->assertResponseRedirects('/');
        $this->em->clear();
        $expertise = $this->em->getRepository(Expertise::class)->findOneBy(['title' => 'Test Expertise']);
        $this->assertNull($expertise);
    }

    public function testRecommendationEditAll(): void
    {
        $client = $this->createAdminClient();

        $this->setupDependencies();
        $crawler = $client->request('GET', '/recommendation/edit-all');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Enregistrer')->form();

        $photoPath = tempnam(sys_get_temp_dir(), 'test_image') . '.png';
        file_put_contents($photoPath, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='));
        $image = new UploadedFile(
            $photoPath,
            'image.png',
            'image/png',
            null,
            true
        );

        $client->request('POST', '/recommendation/edit-all', [
            'form' => [
                'recommendations' => [
                    [
                        'firstname' => 'John',
                        'lastname' => 'Doe',
                        'content' => 'Great person!',
                        'position' => 1
                    ]
                ],
                '_token' => $form['form[_token]']->getValue(),
            ]
        ], [
            'form' => [
                'recommendations' => [
                    ['image' => $image]
                ]
            ]
        ]);

        $this->assertResponseRedirects('/');

        $recommendation = $this->em->getRepository(Recommendation::class)->findOneBy(['firstname' => 'John']);
        $this->assertNotNull($recommendation);
        $this->assertNotNull($recommendation->getImageUrl());
        $this->assertStringContainsString('uploads/images/recommendations/', $recommendation->getImageUrl());
    }

    public function testPersonalInfoEdit(): void
    {
        $client = $this->createAdminClient();

        $this->setupDependencies();
        $crawler = $client->request('GET', '/personal-info/edit');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Enregistrer')->form();
        $form['personal_info[firstname]'] = 'John';
        $form['personal_info[name]'] = 'Smith';
        $client->submit($form);
        $this->assertResponseRedirects('/');

        $info = $this->em->getRepository(PersonalInfo::class)->findOneBy([]);
        $this->assertSame('John', $info->getFirstname());
    }

    public function testSocialEditAll(): void
    {
        $client = $this->createAdminClient();

        $this->setupDependencies();
        $crawler = $client->request('GET', '/social/edit-all');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Enregistrer')->form();

        $client->request('POST', '/social/edit-all', [
            'form' => [
                'socials' => [
                    ['name' => 'LinkedIn', 'url' => 'https://linkedin.com', 'iconClass' => 'linkedin', 'position' => 1]
                ],
                '_token' => $form['form[_token]']->getValue(),
            ]
        ]);

        $this->assertResponseRedirects('/#resume');

        $social = $this->em->getRepository(Social::class)->findOneBy(['name' => 'LinkedIn']);
        $this->assertNotNull($social);

        // Test delete via EditAll
        $client->request('POST', '/social/edit-all', [
            'form' => [
                'socials' => [],
                '_token' => $form['form[_token]']->getValue(),
            ]
        ]);
        $this->assertResponseRedirects('/#resume');
        $this->em->clear();
        $social = $this->em->getRepository(Social::class)->findOneBy(['name' => 'LinkedIn']);
        $this->assertNull($social);
    }
}
