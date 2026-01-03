<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\PersonalInfo;
use App\Form\PersonalInfoType;
use App\Repository\PersonalInfoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/personal-info')]
#[IsGranted('ROLE_ADMIN')]
final class PersonalInfoController extends AbstractController
{
    public function __construct(
        private readonly PersonalInfoRepository $personalInfoRepository,
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator,
    ) {}

    #[Route('/edit', name: 'personal_info_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request): Response
    {
        $personalInfo = $this->personalInfoRepository->findOneOrNew();

        $form = $this->createForm(PersonalInfoType::class, $personalInfo, [
            'action' => $this->generateUrl('personal_info_edit'),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($personalInfo);
            $this->em->flush();

            $this->addFlash('success', $this->translator->trans('personal_info.flash.updated', [], 'messages'));

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['reload' => true]);
            }

            return $this->redirectToRoute('app_home');
        }

        $status = ($form->isSubmitted() && !$form->isValid())
            ? Response::HTTP_UNPROCESSABLE_ENTITY
            : Response::HTTP_OK;

        return $this->render('personal_info/_form_modal_content.html.twig', [
            'form' => $form->createView(),
            'title' => $this->translator->trans('personal_info.modal.title', [], 'messages'),
            'submit_label' => 'global.save',
        ], new Response(null, $status));
    }
}
