<?php

declare(strict_types=1);

namespace App\Portfolio\Presentation\Controller;

use App\Portfolio\Application\Command\UpdateRecommendationsCommand;
use App\Portfolio\Application\Handler\UpdateRecommendationsHandler;
use App\Portfolio\Domain\Repository\RecommendationRepositoryInterface;
use App\Portfolio\Presentation\Form\RecommendationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/recommendation')]
#[IsGranted('ROLE_ADMIN')]
final class RecommendationController extends AbstractController
{
    public function __construct(
        private readonly RecommendationRepositoryInterface $recommendationRepository,
        private readonly UpdateRecommendationsHandler $handler,
        private readonly TranslatorInterface $translator,
    ) {}

    #[Route('/edit-all', name: 'recommendation_edit_all', methods: ['GET', 'POST'])]
    public function editAll(Request $request): Response
    {
        $recommendations = $this->recommendationRepository->findAllOrderedByPosition();

        $form = $this
            ->createFormBuilder(['recommendations' => $recommendations])
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
            $submittedRecommendations = $form->get('recommendations')->getData();

            $imageFiles = [];
            foreach ($submittedRecommendations as $key => $recommendation) {
                $imageFiles[$key] = $form
                    ->get('recommendations')
                    ->get((string) $key)
                    ->get('image')
                    ->getData();
            }

            ($this->handler)(new UpdateRecommendationsCommand($submittedRecommendations, $imageFiles));

            $this->addFlash('success', $this->translator->trans('recommendation.flash.updated', [], 'messages'));

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['reload' => true]);
            }

            return $this->redirectToRoute('app_home');
        }

        $status = $form->isSubmitted() && !$form->isValid() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK;

        return $this->render(
            'about/recommendation/_form_modal_content.html.twig',
            [
                'form' => $form->createView(),
                'title' => $this->translator->trans('recommendation.modal.title', [], 'messages'),
                'submit_label' => 'global.save',
            ],
            new Response(null, $status),
        );
    }
}
