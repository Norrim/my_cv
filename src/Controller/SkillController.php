<?php

declare(strict_types=1);

namespace App\Controller;


use App\Entity\Skill;
use App\Form\SkillType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/skill')]
#[IsGranted('ROLE_ADMIN')]
class SkillController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator,
    ) {}

    #[Route('/new', name: 'skill_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $skill = new Skill();

        $form = $this->createForm(SkillType::class, $skill, [
            'action' => $this->generateUrl('skill_new'),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($skill);
            $this->em->flush();

            // Ajouter le flash AVANT de retourner une réponse JSON, pour qu'il soit
            // disponible après le rechargement effectué côté client.
            $this->addFlash('success', $this->translator->trans('resume.skill.flash.created'));

            if ($request->isXmlHttpRequest()) {
                return $this->json(['reload' => true]);
            }

            return $this->redirectToResume();
        }

        $response = $this->render('resume/skill/_form_modal_content.html.twig', [
            'form' => $form->createView(),
            'title' => $this->translator->trans('resume.skill.modal.new.title'),
            'submit_label' => 'global.save',
        ]);

        if ($form->isSubmitted() && !$form->isValid()) {
            $response->setStatusCode(422);
        }

        return $response;
    }

    #[Route('/{id}/edit', name: 'skill_edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Skill $skill): Response
    {
        $form = $this->createForm(SkillType::class, $skill, [
            'action' => $this->generateUrl('skill_edit', ['id' => $skill->getId()]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            // Ajouter le flash AVANT de retourner une réponse JSON, pour qu'il soit
            // disponible après le rechargement effectué côté client.
            $this->addFlash('success', $this->translator->trans('resume.skill.flash.updated'));

            if ($request->isXmlHttpRequest()) {
                return $this->json(['reload' => true]);
            }

            return $this->redirectToResume();
        }

        $response = $this->render('resume/skill/_form_modal_content.html.twig', [
            'form' => $form->createView(),
            'title' => $this->translator->trans('resume.skill.modal.edit.title'),
            'submit_label' => 'global.save',
        ]);

        if ($form->isSubmitted() && !$form->isValid()) {
            $response->setStatusCode(422);
        }

        return $response;
    }

    #[Route('/{id}/delete', name: 'skill_delete', requirements: ['id' => '\\d+'], methods: ['POST'])]
    public function delete(Request $request, Skill $skill): Response
    {
        if (!$this->isCsrfTokenValid('delete_skill_' . $skill->getId(), (string)$request->request->get('_token'))) {
            $this->addFlash('error', $this->translator->trans('resume.skill.flash.csrf_invalid'));
            return $this->redirectToResume();
        }

        $this->em->remove($skill);
        $this->em->flush();

        $this->addFlash('error', $this->translator->trans('resume.skill.flash.deleted'));

        return $this->redirectToResume();
    }

    private function redirectToResume(): RedirectResponse
    {
        $url = $this->generateUrl('app_home') . '#resume';
        return new RedirectResponse($url);
    }
}
