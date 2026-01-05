<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ClientRepository;
use App\Repository\EducationRepository;
use App\Repository\ExperienceRepository;
use App\Repository\ExpertiseRepository;
use App\Repository\PersonalInfoRepository;
use App\Repository\RecommendationRepository;
use App\Repository\SkillRepository;
use App\Repository\SocialRepository;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    public function __construct(
        private readonly EducationRepository $educationRepository,
        private readonly ExperienceRepository $experienceRepository,
        private readonly SkillRepository $skillRepository,
        private readonly SocialRepository $socialRepository,
        private readonly PersonalInfoRepository $personalInfoRepository,
        private readonly ClientRepository $clientRepository,
        private readonly RecommendationRepository $recommendationRepository,
        private readonly ExpertiseRepository $expertiseRepository,
    ) {}

    #[Route(path: '/', name: 'app_home', methods: ['GET'])]
    public function index(): Response
    {
        $educations = $this->educationRepository->findBy([], ['position' => 'ASC']);
        $experiences = $this->experienceRepository->findBy([], ['position' => 'ASC']);
        $skills = $this->skillRepository->findBy([], ['position' => 'ASC']);
        $socials = $this->socialRepository->findBy([], ['id' => 'ASC']);
        $personalInfo = $this->personalInfoRepository->findOneBy([]);
        $clients = $this->clientRepository->findBy([], ['position' => 'ASC']);
        $recommendations = $this->recommendationRepository->findBy([], ['position' => 'ASC']);
        $expertises = $this->expertiseRepository->findBy([], ['position' => 'ASC']);

        $form = $this->createForm(ContactType::class);

        return $this->render('pages/home.html.twig', [
            'educations' => $educations,
            'experiences' => $experiences,
            'skills' => $skills,
            'socials' => $socials,
            'personalInfo' => $personalInfo,
            'clients' => $clients,
            'recommendations' => $recommendations,
            'expertises' => $expertises,
            'contactForm' => $form->createView(),
        ]);
    }
}
