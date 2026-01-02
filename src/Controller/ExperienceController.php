<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Experience;
use App\Form\ExperienceType;
use App\Handler\CrudHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        private readonly CrudHandler $crudHandler,
        private readonly TranslatorInterface $translator,
    ) {}

    #[Route('/new', name: 'experience_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        return $this->crudHandler->handleNew(
            $request,
            new Experience(),
            ExperienceType::class,
            'experience_new',
            'resume/experience/_form_modal_content.html.twig',
            $this->translator->trans('resume.experience.modal.title')
        );
    }

    #[Route('/{id}/edit', name: 'experience_edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Experience $experience): Response
    {
        return $this->crudHandler->handleEdit(
            $request,
            $experience,
            ExperienceType::class,
            'experience_edit',
            'resume/experience/_form_modal_content.html.twig',
            $this->translator->trans('resume.experience.modal.title')
        );
    }

    #[Route('/{id}/delete', name: 'experience_delete', requirements: ['id' => '\\d+'], methods: ['POST'])]
    public function delete(Request $request, Experience $experience): Response
    {
        return $this->crudHandler->handleDelete(
            $request,
            $experience,
            'experience',
            $this->translator->trans('resume.experience.modal.title')
        );
    }
}
