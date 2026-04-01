<?php

declare(strict_types=1);

namespace App\Home\Presentation\Controller;

use App\Contact\Application\Service\ContactMailerService;
use App\Contact\Domain\Dto\ContactDataDto;
use App\Contact\Presentation\Form\ContactFlowType;
use App\Identity\Infrastructure\Doctrine\PersonalInfoRepository;
use App\Identity\Infrastructure\Doctrine\SocialRepository;
use App\Portfolio\Infrastructure\Doctrine\ClientRepository;
use App\Portfolio\Infrastructure\Doctrine\ExpertiseRepository;
use App\Portfolio\Infrastructure\Doctrine\RecommendationRepository;
use App\Resume\Infrastructure\Doctrine\EducationRepository;
use App\Resume\Infrastructure\Doctrine\ExperienceRepository;
use App\Resume\Infrastructure\Doctrine\SkillRepository;
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
            'educations' => $this->educationRepository->findAllOrderedByPosition(),
            'experiences' => $this->experienceRepository->findAllOrderedByPosition(),
            'skills' => $this->skillRepository->findAllOrderedByPosition(),
            'socials' => $this->socialRepository->findBy([], ['id' => 'ASC']),
            'personalInfo' => $this->personalInfoRepository->findOneBy([]),
            'clients' => $this->clientRepository->findAllOrderedByPosition(),
            'recommendations' => $this->recommendationRepository->findAllOrderedByPosition(),
            'expertises' => $this->expertiseRepository->findAllOrderedByPosition(),
            'contactForm' => $flow->getStepForm(),
            'contactFormActive' => $contactFormActive,
        ]);
    }
}
