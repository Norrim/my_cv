<?php

namespace App\Controller;

use App\Repository\EducationRepository;
use App\Repository\ExperienceRepository;
use App\Repository\SkillRepository;
use App\Repository\SocialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomeController extends AbstractController
{
    public function __construct(
        private readonly EducationRepository $educationRepository,
        private readonly ExperienceRepository $experienceRepository,
        private readonly SkillRepository $skillRepository,
        private readonly SocialRepository $socialRepository,
    ){}

    #[Route(path: '/', name: 'app_home', methods: ['GET'])]
    public function index(EducationRepository $educationRepository): Response
    {
        $educations = $this->educationRepository->findBy([], ['position' => 'ASC']);
        $experiences = $this->experienceRepository->findBy([], ['position' => 'ASC']);
        $skills = $this->skillRepository->findBy([], ['position' => 'ASC']);
        $socials = $this->socialRepository->findBy([], ['id' => 'ASC']);

        return $this->render('home/index.html.twig', [
            'educations' => $educations,
            'experiences' => $experiences,
            'skills' => $skills,
            'socials' => $socials,
        ]);
    }
}
