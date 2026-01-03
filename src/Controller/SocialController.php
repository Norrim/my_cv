<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\SocialRepository;
use App\Form\SocialType;
use Doctrine\ORM\EntityManagerInterface;
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
        private readonly SocialRepository $socialRepository,
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator,
    ) {}

    #[Route('/edit-all', name: 'social_edit_all', methods: ['GET', 'POST'])]
    public function editAll(Request $request): Response
    {
        $socials = $this->socialRepository->findBy([], ['position' => 'ASC']);

        $form = $this->createFormBuilder(['socials' => $socials])
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
            // Get current socials from database
            $existingSocials = $this->socialRepository->findAll();
            $submittedSocials = $form->get('socials')->getData();

            // Remove socials that are no longer in the submitted list
            foreach ($existingSocials as $existingSocial) {
                if (!in_array($existingSocial, $submittedSocials, true)) {
                    $this->em->remove($existingSocial);
                }
            }

            foreach ($submittedSocials as $social) {
                $this->em->persist($social);
            }
            $this->em->flush();

            $this->addFlash('success', $this->translator->trans('social.flash.updated', [], 'messages'));

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['reload' => true]);
            }

            return $this->redirectToRoute('app_home', ['_fragment' => 'resume']);
        }

        $status = ($form->isSubmitted() && !$form->isValid())
            ? Response::HTTP_UNPROCESSABLE_ENTITY
            : Response::HTTP_OK;

        return $this->render('social/_form_modal_content.html.twig', [
            'form' => $form->createView(),
            'title' => $this->translator->trans('social.modal.title', [], 'messages'),
            'submit_label' => 'global.save',
        ], new Response(null, $status));
    }
}
