<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Experience;
use App\Form\ExperienceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/experience')]
#[IsGranted('ROLE_ADMIN')]
class ExperienceController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator,
    ) {}

    #[Route('/new', name: 'experience_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $experience = new Experience();

        $form = $this->createForm(ExperienceType::class, $experience, [
            'action' => $this->generateUrl('experience_new'),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($experience);
            $this->em->flush();

            // Ajouter le flash AVANT de retourner une réponse JSON, pour qu'il soit
            // disponible après le rechargement effectué côté client.
            $this->addFlash('success', $this->translator->trans('resume.experience.flash.created'));

            if ($request->isXmlHttpRequest()) {
                return $this->json(['reload' => true]);
            }

            return $this->redirectToResume();
        }

        $response = $this->render('resume/experience/_form_modal_content.html.twig', [
            'form' => $form->createView(),
            'title' => $this->translator->trans('resume.experience.modal.new.title'),
            'submit_label' => 'global.save',
        ]);

        if ($form->isSubmitted() && !$form->isValid()) {
            $response->setStatusCode(422);
        }

        return $response;
    }

    #[Route('/{id}/edit', name: 'experience_edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Experience $experience): Response
    {
        $form = $this->createForm(ExperienceType::class, $experience, [
            'action' => $this->generateUrl('experience_edit', ['id' => $experience->getId()]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            // Ajouter le flash AVANT de retourner une réponse JSON, pour qu'il soit
            // disponible après le rechargement effectué côté client.
            $this->addFlash('success', $this->translator->trans('resume.experience.flash.updated'));

            if ($request->isXmlHttpRequest()) {
                return $this->json(['reload' => true]);
            }

            return $this->redirectToResume();
        }

        $response = $this->render('resume/experience/_form_modal_content.html.twig', [
            'form' => $form->createView(),
            'title' => $this->translator->trans('resume.experience.modal.edit.title'),
            'submit_label' => 'global.save',
        ]);

        if ($form->isSubmitted() && !$form->isValid()) {
            $response->setStatusCode(422);
        }

        return $response;
    }

    #[Route('/{id}/delete', name: 'experience_delete', requirements: ['id' => '\\d+'], methods: ['POST'])]
    public function delete(Request $request, Experience $experience): Response
    {
        if (!$this->isCsrfTokenValid('delete_experience_' . $experience->getId(), (string)$request->request->get('_token'))) {
            $this->addFlash('error', $this->translator->trans('resume.experience.flash.csrf_invalid'));
            return $this->redirectToResume();
        }

        $this->em->remove($experience);
        $this->em->flush();

        $this->addFlash('error', $this->translator->trans('resume.experience.flash.deleted'));

        return $this->redirectToResume();
    }

    private function redirectToResume(): RedirectResponse
    {
        $url = $this->generateUrl('app_home') . '#resume';
        return new RedirectResponse($url);
    }
}
