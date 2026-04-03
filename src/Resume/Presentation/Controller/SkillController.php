<?php

declare(strict_types=1);

namespace App\Resume\Presentation\Controller;

use App\Resume\Application\Command\DeleteSkillCommand;
use App\Resume\Application\Command\SaveSkillCommand;
use App\Resume\Application\Handler\DeleteSkillHandler;
use App\Resume\Application\Handler\SaveSkillHandler;
use App\Resume\Domain\Entity\Skill;
use App\Resume\Presentation\Form\SkillType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/skill')]
#[IsGranted('ROLE_ADMIN')]
final class SkillController extends AbstractController
{
    public function __construct(
        private readonly SaveSkillHandler $saveHandler,
        private readonly DeleteSkillHandler $deleteHandler,
        private readonly TranslatorInterface $translator,
    ) {}

    #[Route('/new', name: 'skill_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $skill = new Skill();
        $label = $this->translator->trans('resume.skill.modal.title');

        $form = $this->createForm(SkillType::class, $skill, [
            'action' => $this->generateUrl('skill_new'),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            ($this->saveHandler)(new SaveSkillCommand($skill));

            $this->addFlash('success', $this->translator->trans('crud.flash.created', ['%label%' => $label]));

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['reload' => true]);
            }

            return $this->redirectToRoute('app_home', ['_fragment' => 'resume']);
        }

        $status = $form->isSubmitted() && !$form->isValid() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK;

        return $this->render(
            'resume/skill/_form_modal_content.html.twig',
            [
                'form' => $form->createView(),
                'title' => $this->translator->trans('crud.modal.new', ['%label%' => $label]),
                'submit_label' => 'global.save',
            ],
            new Response(null, $status),
        );
    }

    #[Route('/{id}/edit', name: 'skill_edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Skill $skill): Response
    {
        $label = $this->translator->trans('resume.skill.modal.title');

        $form = $this->createForm(SkillType::class, $skill, [
            'action' => $this->generateUrl('skill_edit', ['id' => $skill->getId()]),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            ($this->saveHandler)(new SaveSkillCommand($skill));

            $this->addFlash('success', $this->translator->trans('crud.flash.updated', ['%label%' => $label]));

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['reload' => true]);
            }

            return $this->redirectToRoute('app_home', ['_fragment' => 'resume']);
        }

        $status = $form->isSubmitted() && !$form->isValid() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK;

        return $this->render(
            'resume/skill/_form_modal_content.html.twig',
            [
                'form' => $form->createView(),
                'title' => $this->translator->trans('crud.modal.edit', ['%label%' => $label]),
                'submit_label' => 'global.save',
            ],
            new Response(null, $status),
        );
    }

    #[Route('/{id}/delete', name: 'skill_delete', requirements: ['id' => '\\d+'], methods: ['POST'])]
    public function delete(Request $request, Skill $skill): Response
    {
        $label = $this->translator->trans('resume.skill.modal.title');
        $tokenName = 'delete_skill_' . $skill->getId();

        if (!$this->isCsrfTokenValid($tokenName, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        ($this->deleteHandler)(new DeleteSkillCommand($skill));

        $this->addFlash('success', $this->translator->trans('crud.flash.deleted', ['%label%' => $label]));

        return $this->redirectToRoute('app_home', ['_fragment' => 'resume']);
    }
}
