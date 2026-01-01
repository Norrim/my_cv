<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Education;
use App\Form\EducationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/education')]
#[IsGranted('ROLE_ADMIN')]
class EducationController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator,
    ) {}

    #[Route('/new', name: 'education_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $education = new Education();

        $form = $this->createForm(EducationType::class, $education, [
            'action' => $this->generateUrl('education_new'),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($education);
            $this->em->flush();

            // Ajouter le flash AVANT de retourner une réponse JSON, pour qu'il soit
            // disponible après le rechargement effectué côté client.
            $this->addFlash('success', $this->translator->trans('resume.education.flash.created'));

            if ($request->isXmlHttpRequest()) {
                return $this->json(['reload' => true]);
            }

            return $this->redirectToResume();
        }

        $response = $this->render('resume/education/_form_modal_content.html.twig', [
            'form' => $form->createView(),
            'title' => $this->translator->trans('resume.education.modal.new.title'),
            'submit_label' => 'global.save',
        ]);

        if ($form->isSubmitted() && !$form->isValid()) {
            $response->setStatusCode(422);
        }

        return $response;
    }

    #[Route('/{id}/edit', name: 'education_edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Education $education): Response
    {
        $form = $this->createForm(EducationType::class, $education, [
            'action' => $this->generateUrl('education_edit', ['id' => $education->getId()]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            // Ajouter le flash AVANT de retourner une réponse JSON, pour qu'il soit
            // disponible après le rechargement effectué côté client.
            $this->addFlash('success', $this->translator->trans('resume.education.flash.updated'));

            if ($request->isXmlHttpRequest()) {
                return $this->json(['reload' => true]);
            }

            return $this->redirectToResume();
        }

        $response = $this->render('resume/education/_form_modal_content.html.twig', [
            'form' => $form->createView(),
            'title' => $this->translator->trans('resume.education.modal.edit.title'),
            'submit_label' => 'global.save',
        ]);

        if ($form->isSubmitted() && !$form->isValid()) {
            $response->setStatusCode(422);
        }

        return $response;
    }

    #[Route('/{id}/delete', name: 'education_delete', requirements: ['id' => '\\d+'], methods: ['POST'])]
    public function delete(Request $request, Education $education): Response
    {
        if (!$this->isCsrfTokenValid('delete_education_' . $education->getId(), (string)$request->request->get('_token'))) {
            $this->addFlash('error', $this->translator->trans('resume.education.flash.csrf_invalid'));
            return $this->redirectToResume();
        }

        $this->em->remove($education);
        $this->em->flush();

        $this->addFlash('error', $this->translator->trans('resume.education.flash.deleted'));

        return $this->redirectToResume();
    }

    private function redirectToResume(): RedirectResponse
    {
        $url = $this->generateUrl('app_home') . '#resume';
        return new RedirectResponse($url);
    }
}
