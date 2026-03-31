<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Contact\ContactDataDto;
use App\Form\Contact\ContactFlowType;
use App\Repository\ClientRepository;
use App\Repository\EducationRepository;
use App\Repository\ExperienceRepository;
use App\Repository\ExpertiseRepository;
use App\Repository\PersonalInfoRepository;
use App\Repository\RecommendationRepository;
use App\Repository\SkillRepository;
use App\Repository\SocialRepository;
use App\Service\ContactMailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Flow\FormFlowInterface;
use Symfony\Component\HttpFoundation\Request;
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
        private readonly ContactMailerService $contactMailer,
    ) {}

    #[Route(path: '/', name: 'app_home', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $contactData = new ContactDataDto();

        /** @var FormFlowInterface $flow */
        $flow = $this->createForm(ContactFlowType::class, $contactData)
            ->handleRequest($request);

        $contactFormActive = $flow->isSubmitted();

        if ($flow->isSubmitted() && $flow->isValid() && $flow->isFinished()) {
            /** @var ContactDataDto $data */
            $data = $flow->getData();
            $this->contactMailer->send($data);
            $flow->reset();

            $this->addFlash('success', 'Votre message a bien été envoyé !');

            return $this->redirectToRoute('app_home', ['_fragment' => 'contact']);
        }

        return $this->render('pages/home.html.twig', [
            'educations' => $this->educationRepository->findBy([], ['position' => 'ASC']),
            'experiences' => $this->experienceRepository->findBy([], ['position' => 'ASC']),
            'skills' => $this->skillRepository->findBy([], ['position' => 'ASC']),
            'socials' => $this->socialRepository->findBy([], ['id' => 'ASC']),
            'personalInfo' => $this->personalInfoRepository->findOneBy([]),
            'clients' => $this->clientRepository->findBy([], ['position' => 'ASC']),
            'recommendations' => $this->recommendationRepository->findBy([], ['position' => 'ASC']),
            'expertises' => $this->expertiseRepository->findBy([], ['position' => 'ASC']),
            'contactForm' => $flow->getStepForm(),
            'contactFormActive' => $contactFormActive,
        ]);
    }
}
