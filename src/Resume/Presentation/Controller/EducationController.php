<?php

declare(strict_types=1);

namespace App\Resume\Presentation\Controller;

use App\Resume\Application\Command\DeleteEducationCommand;
use App\Resume\Application\Command\SaveEducationCommand;
use App\Resume\Application\Handler\DeleteEducationHandler;
use App\Resume\Application\Handler\SaveEducationHandler;
use App\Resume\Domain\Entity\Education;
use App\Resume\Presentation\Form\EducationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/education')]
#[IsGranted('ROLE_ADMIN')]
final class EducationController extends AbstractController
{
    public function __construct(
        private readonly SaveEducationHandler $saveHandler,
        private readonly DeleteEducationHandler $deleteHandler,
        private readonly TranslatorInterface $translator,
    ) {}

    #[Route('/new', name: 'education_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $education = new Education();
        $label = $this->translator->trans('resume.education.modal.title');

        $form = $this->createForm(EducationType::class, $education, [
            'action' => $this->generateUrl('education_new'),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            ($this->saveHandler)(new SaveEducationCommand($education));

            $this->addFlash('success', $this->translator->trans('crud.flash.created', ['%label%' => $label]));

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['reload' => true]);
            }

            return $this->redirectToRoute('app_home', ['_fragment' => 'resume']);
        }

        $status = ($form->isSubmitted() && !$form->isValid())
            ? Response::HTTP_UNPROCESSABLE_ENTITY
            : Response::HTTP_OK;

        return $this->render('resume/education/_form_modal_content.html.twig', [
            'form' => $form->createView(),
            'title' => $this->translator->trans('crud.modal.new', ['%label%' => $label]),
            'submit_label' => 'global.save',
        ], new Response(null, $status));
    }

    #[Route('/{id}/edit', name: 'education_edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Education $education): Response
    {
        $label = $this->translator->trans('resume.education.modal.title');

        $form = $this->createForm(EducationType::class, $education, [
            'action' => $this->generateUrl('education_edit', ['id' => $education->getId()]),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            ($this->saveHandler)(new SaveEducationCommand($education));

            $this->addFlash('success', $this->translator->trans('crud.flash.updated', ['%label%' => $label]));

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['reload' => true]);
            }

            return $this->redirectToRoute('app_home', ['_fragment' => 'resume']);
        }

        $status = ($form->isSubmitted() && !$form->isValid())
            ? Response::HTTP_UNPROCESSABLE_ENTITY
            : Response::HTTP_OK;

        return $this->render('resume/education/_form_modal_content.html.twig', [
            'form' => $form->createView(),
            'title' => $this->translator->trans('crud.modal.edit', ['%label%' => $label]),
            'submit_label' => 'global.save',
        ], new Response(null, $status));
    }

    #[Route('/{id}/delete', name: 'education_delete', requirements: ['id' => '\\d+'], methods: ['POST'])]
    public function delete(Request $request, Education $education): Response
    {
        $label = $this->translator->trans('resume.education.modal.title');
        $tokenName = 'delete_education_' . $education->getId();

        if (!$this->isCsrfTokenValid($tokenName, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        ($this->deleteHandler)(new DeleteEducationCommand($education));

        $this->addFlash('success', $this->translator->trans('crud.flash.deleted', ['%label%' => $label]));

        return $this->redirectToRoute('app_home', ['_fragment' => 'resume']);
    }
}
