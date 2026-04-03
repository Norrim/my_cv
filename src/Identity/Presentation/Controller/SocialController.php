<?php

declare(strict_types=1);

namespace App\Identity\Presentation\Controller;

use App\Identity\Application\Command\UpdateSocialsCommand;
use App\Identity\Application\Handler\UpdateSocialsHandler;
use App\Identity\Domain\Repository\SocialRepositoryInterface;
use App\Identity\Presentation\Form\SocialType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/social')]
#[IsGranted('ROLE_ADMIN')]
final class SocialController extends AbstractController
{
    public function __construct(
        private readonly SocialRepositoryInterface $socialRepository,
        private readonly UpdateSocialsHandler $handler,
        private readonly TranslatorInterface $translator,
    ) {}

    #[Route('/edit-all', name: 'social_edit_all', methods: ['GET', 'POST'])]
    public function editAll(Request $request): Response
    {
        $socials = $this->socialRepository->findAllOrderedByPosition();

        $form = $this
            ->createFormBuilder(['socials' => $socials])
            ->setAction($this->generateUrl('social_edit_all'))
            ->setMethod('POST')
            ->add('socials', CollectionType::class, [
                'entry_type' => SocialType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            ($this->handler)(new UpdateSocialsCommand($form->get('socials')->getData()));

            $this->addFlash('success', $this->translator->trans('social.flash.updated', [], 'messages'));

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['reload' => true]);
            }

            return $this->redirectToRoute('app_home', ['_fragment' => 'resume']);
        }

        $status = $form->isSubmitted() && !$form->isValid() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK;

        return $this->render(
            'social/_form_modal_content.html.twig',
            [
                'form' => $form->createView(),
                'title' => $this->translator->trans('social.modal.title', [], 'messages'),
                'submit_label' => 'global.save',
            ],
            new Response(null, $status),
        );
    }
}
