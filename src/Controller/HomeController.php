<?php

namespace App\Controller;

use App\Repository\EducationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomeController extends AbstractController
{
    #[Route(path: '/', name: 'app_home', methods: ['GET'])]
    public function index(EducationRepository $educationRepository): Response
    {
        $educations = $educationRepository->findBy([], ['position' => 'ASC']);

        return $this->render('home/index.html.twig', [
            'educations' => $educations,
        ]);
    }
}
