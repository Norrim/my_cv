<?php

declare(strict_types=1);

namespace App\Controller;


use App\Entity\Skill;
use App\Form\SkillType;
use App\Handler\CrudHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        private readonly CrudHandler $crudHandler,
        private readonly TranslatorInterface $translator,
    ) {}

    #[Route('/new', name: 'skill_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        return $this->crudHandler->handleNew(
            $request,
            new Skill(),
            SkillType::class,
            'skill_new',
            'resume/skill/_form_modal_content.html.twig',
            $this->translator->trans('resume.skill.modal.title')
        );
    }

    #[Route('/{id}/edit', name: 'skill_edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Skill $skill): Response
    {
        return $this->crudHandler->handleEdit(
            $request,
            $skill,
            SkillType::class,
            'skill_edit',
            'resume/skill/_form_modal_content.html.twig',
            $this->translator->trans('resume.skill.modal.title')
        );
    }

    #[Route('/{id}/delete', name: 'skill_delete', requirements: ['id' => '\\d+'], methods: ['POST'])]
    public function delete(Request $request, Skill $skill): Response
    {
        return $this->crudHandler->handleDelete(
            $request,
            $skill,
            'skill',
            $this->translator->trans('resume.skill.modal.title')
        );
    }
}
