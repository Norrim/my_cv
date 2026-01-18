<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Recommendation;
use App\Form\RecommendationType;
use App\Repository\RecommendationRepository;
use App\Service\RecommendationFileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/recommendation')]
final class RecommendationController extends AbstractController
{
    public function __construct(
        private readonly RecommendationRepository $recommendationRepository,
        private readonly EntityManagerInterface $em,
        private readonly RecommendationFileUploader $fileUploader,
        private readonly TranslatorInterface $translator,
    ) {}

    #[Route('/edit-all', name: 'recommendation_edit_all', methods: ['GET', 'POST'])]
    public function editAll(Request $request): Response
    {
        $recommendations = $this->recommendationRepository->findBy([], ['position' => 'ASC']);

        $form = $this->createFormBuilder(['recommendations' => $recommendations])
            ->setAction($this->generateUrl('recommendation_edit_all'))
            ->setMethod('POST')
            ->add('recommendations', CollectionType::class, [
                'entry_type' => RecommendationType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingRecommendations = $this->recommendationRepository->findAll();
            $submittedRecommendations = $form->get('recommendations')->getData();

            // Remove recommendations that are no longer in the submitted list
            foreach ($existingRecommendations as $existingRecommendation) {
                if (!in_array($existingRecommendation, $submittedRecommendations, true)) {
                    // Delete the physical file before removing from DB
                    if ($existingRecommendation->getImageUrl()) {
                        $this->fileUploader->remove(basename($existingRecommendation->getImageUrl()));
                    }
                    $this->em->remove($existingRecommendation);
                }
            }

            foreach ($submittedRecommendations as $key => $recommendation) {
                $recommendationForm = $form->get('recommendations')->get((string) $key);
                $imageFile = $recommendationForm->get('image')->getData();

                if ($imageFile) {
                    // Delete old file if exists
                    if ($recommendation->getImageUrl()) {
                        $this->fileUploader->remove(basename($recommendation->getImageUrl()));
                    }

                    $imageFileName = $this->fileUploader->upload($imageFile);
                    $recommendation->setImageUrl('uploads/images/recommendations/' . $imageFileName);
                }

                $this->em->persist($recommendation);
            }
            $this->em->flush();

            $this->addFlash('success', $this->translator->trans('recommendation.flash.updated', [], 'messages'));

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['reload' => true]);
            }

            return $this->redirectToRoute('app_home');
        }

        $status = ($form->isSubmitted() && !$form->isValid())
            ? Response::HTTP_UNPROCESSABLE_ENTITY
            : Response::HTTP_OK;

        return $this->render('about/recommendation/_form_modal_content.html.twig', [
            'form' => $form->createView(),
            'title' => $this->translator->trans('recommendation.modal.title', [], 'messages'),
            'submit_label' => 'global.save',
        ], new Response(null, $status));
    }
}
