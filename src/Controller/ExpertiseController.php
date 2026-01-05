<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Expertise;
use App\Form\ExpertiseType;
use App\Repository\ExpertiseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/expertise')]
final class ExpertiseController extends AbstractController
{
    public function __construct(
        private readonly ExpertiseRepository $expertiseRepository,
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator,
    ) {}

    #[Route('/edit-all', name: 'expertise_edit_all', methods: ['GET', 'POST'])]
    public function editAll(Request $request): Response
    {
        $expertises = $this->expertiseRepository->findBy([], ['position' => 'ASC']);

        $form = $this->createFormBuilder(['expertises' => $expertises])
            ->setAction($this->generateUrl('expertise_edit_all'))
            ->setMethod('POST')
            ->add('expertises', CollectionType::class, [
                'entry_type' => ExpertiseType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingExpertises = $this->expertiseRepository->findAll();
            $submittedExpertises = $form->get('expertises')->getData();

            foreach ($existingExpertises as $existingExpertise) {
                if (!in_array($existingExpertise, $submittedExpertises, true)) {
                    $this->em->remove($existingExpertise);
                }
            }

            foreach ($submittedExpertises as $expertise) {
                $this->em->persist($expertise);
            }
            $this->em->flush();

            $this->addFlash('success', $this->translator->trans('expertise.flash.updated', [], 'messages'));

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['reload' => true]);
            }

            return $this->redirectToRoute('app_home');
        }

        $status = ($form->isSubmitted() && !$form->isValid())
            ? Response::HTTP_UNPROCESSABLE_ENTITY
            : Response::HTTP_OK;

        return $this->render('about/expertise/_form_modal_content.html.twig', [
            'form' => $form->createView(),
            'title' => $this->translator->trans('expertise.modal.title', [], 'messages'),
            'submit_label' => 'global.save',
        ], new Response(null, $status));
    }
}
