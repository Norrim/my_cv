<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Education;
use App\Form\EducationType;
use App\Handler\CrudHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        private readonly CrudHandler $crudHandler,
        private readonly TranslatorInterface $translator,
    ) {}

    #[Route('/new', name: 'education_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        return $this->crudHandler->handleNew(
            $request,
            new Education(),
            EducationType::class,
            'education_new',
            'resume/education/_form_modal_content.html.twig',
            $this->translator->trans('resume.education.modal.title')
        );
    }

    #[Route('/{id}/edit', name: 'education_edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Education $education): Response
    {
        return $this->crudHandler->handleEdit(
            $request,
            $education,
            EducationType::class,
            'education_edit',
            'resume/education/_form_modal_content.html.twig',
            $this->translator->trans('resume.education.modal.title')
        );
    }

    #[Route('/{id}/delete', name: 'education_delete', requirements: ['id' => '\\d+'], methods: ['POST'])]
    public function delete(Request $request, Education $education): Response
    {
        return $this->crudHandler->handleDelete(
            $request,
            $education,
            'education',
            $this->translator->trans('resume.education.modal.title')
        );
    }
}
