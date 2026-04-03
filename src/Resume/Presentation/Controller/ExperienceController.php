<?php

declare(strict_types=1);

namespace App\Resume\Presentation\Controller;

use App\Resume\Application\Command\DeleteExperienceCommand;
use App\Resume\Application\Command\SaveExperienceCommand;
use App\Resume\Application\Handler\DeleteExperienceHandler;
use App\Resume\Application\Handler\SaveExperienceHandler;
use App\Resume\Domain\Entity\Experience;
use App\Resume\Presentation\Form\ExperienceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/experience')]
#[IsGranted('ROLE_ADMIN')]
final class ExperienceController extends AbstractController
{
    public function __construct(
        private readonly SaveExperienceHandler $saveHandler,
        private readonly DeleteExperienceHandler $deleteHandler,
        private readonly TranslatorInterface $translator,
    ) {}

    #[Route('/new', name: 'experience_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $experience = new Experience();
        $label = $this->translator->trans('resume.experience.modal.title');

        $form = $this->createForm(ExperienceType::class, $experience, [
            'action' => $this->generateUrl('experience_new'),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            ($this->saveHandler)(new SaveExperienceCommand($experience));

            $this->addFlash('success', $this->translator->trans('crud.flash.created', ['%label%' => $label]));

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['reload' => true]);
            }

            return $this->redirectToRoute('app_home', ['_fragment' => 'resume']);
        }

        $status = $form->isSubmitted() && !$form->isValid() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK;

        return $this->render(
            'resume/experience/_form_modal_content.html.twig',
            [
                'form' => $form->createView(),
                'title' => $this->translator->trans('crud.modal.new', ['%label%' => $label]),
                'submit_label' => 'global.save',
            ],
            new Response(null, $status),
        );
    }

    #[Route('/{id}/edit', name: 'experience_edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Experience $experience): Response
    {
        $label = $this->translator->trans('resume.experience.modal.title');

        $form = $this->createForm(ExperienceType::class, $experience, [
            'action' => $this->generateUrl('experience_edit', ['id' => $experience->getId()]),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            ($this->saveHandler)(new SaveExperienceCommand($experience));

            $this->addFlash('success', $this->translator->trans('crud.flash.updated', ['%label%' => $label]));

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['reload' => true]);
            }

            return $this->redirectToRoute('app_home', ['_fragment' => 'resume']);
        }

        $status = $form->isSubmitted() && !$form->isValid() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK;

        return $this->render(
            'resume/experience/_form_modal_content.html.twig',
            [
                'form' => $form->createView(),
                'title' => $this->translator->trans('crud.modal.edit', ['%label%' => $label]),
                'submit_label' => 'global.save',
            ],
            new Response(null, $status),
        );
    }

    #[Route('/{id}/delete', name: 'experience_delete', requirements: ['id' => '\\d+'], methods: ['POST'])]
    public function delete(Request $request, Experience $experience): Response
    {
        $label = $this->translator->trans('resume.experience.modal.title');
        $tokenName = 'delete_experience_' . $experience->getId();

        if (!$this->isCsrfTokenValid($tokenName, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        ($this->deleteHandler)(new DeleteExperienceCommand($experience));

        $this->addFlash('success', $this->translator->trans('crud.flash.deleted', ['%label%' => $label]));

        return $this->redirectToRoute('app_home', ['_fragment' => 'resume']);
    }
}
