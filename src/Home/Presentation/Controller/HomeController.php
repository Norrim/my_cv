<?php

declare(strict_types=1);

namespace App\Home\Presentation\Controller;

use App\Contact\Infrastructure\Mailer\ContactMailerService;
use App\Contact\Application\DTO\ContactDataDto;
use App\Contact\Presentation\Form\ContactFlowType;
use App\Identity\Domain\Repository\PersonalInfoRepositoryInterface;
use App\Identity\Domain\Repository\SocialRepositoryInterface;
use App\Portfolio\Domain\Repository\ClientRepositoryInterface;
use App\Portfolio\Domain\Repository\ExpertiseRepositoryInterface;
use App\Portfolio\Domain\Repository\RecommendationRepositoryInterface;
use App\Resume\Domain\Repository\EducationRepositoryInterface;
use App\Resume\Domain\Repository\ExperienceRepositoryInterface;
use App\Resume\Domain\Repository\SkillRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Flow\FormFlowInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    public function __construct(
        private readonly EducationRepositoryInterface $educationRepository,
        private readonly ExperienceRepositoryInterface $experienceRepository,
        private readonly SkillRepositoryInterface $skillRepository,
        private readonly SocialRepositoryInterface $socialRepository,
        private readonly PersonalInfoRepositoryInterface $personalInfoRepository,
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly RecommendationRepositoryInterface $recommendationRepository,
        private readonly ExpertiseRepositoryInterface $expertiseRepository,
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
            'socials' => $this->socialRepository->findAllOrderedByPosition(),
            'personalInfo' => $this->personalInfoRepository->findFirst(),
            'clients' => $this->clientRepository->findAllOrderedByPosition(),
            'recommendations' => $this->recommendationRepository->findAllOrderedByPosition(),
            'expertises' => $this->expertiseRepository->findAllOrderedByPosition(),
            'contactForm' => $flow->getStepForm(),
            'contactFormActive' => $contactFormActive,
        ]);
    }
}
