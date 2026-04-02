<?php

declare(strict_types=1);

namespace App\Portfolio\Presentation\Controller;

use App\Portfolio\Application\Command\UpdateClientsCommand;
use App\Portfolio\Application\Handler\UpdateClientsHandler;
use App\Portfolio\Domain\Repository\ClientRepositoryInterface;
use App\Portfolio\Presentation\Form\ClientType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/client')]
#[IsGranted('ROLE_ADMIN')]
final class ClientController extends AbstractController
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly UpdateClientsHandler $handler,
        private readonly TranslatorInterface $translator,
    ) {}

    #[Route('/edit-all', name: 'client_edit_all', methods: ['GET', 'POST'])]
    public function editAll(Request $request): Response
    {
        $clients = $this->clientRepository->findAllOrderedByPosition();

        $form = $this->createFormBuilder(['clients' => $clients])
            ->setAction($this->generateUrl('client_edit_all'))
            ->setMethod('POST')
            ->add('clients', CollectionType::class, [
                'entry_type' => ClientType::class,
                'entry_options' => ['require_logo' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $submittedClients = $form->get('clients')->getData();

            $logoFiles = [];
            foreach ($submittedClients as $key => $client) {
                $logoFiles[$key] = $form->get('clients')->get((string) $key)->get('logo')->getData();
            }

            ($this->handler)(new UpdateClientsCommand($submittedClients, $logoFiles));

            $this->addFlash('success', $this->translator->trans('client.flash.updated', [], 'messages'));

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['reload' => true]);
            }

            return $this->redirectToRoute('app_home');
        }

        $status = ($form->isSubmitted() && !$form->isValid())
            ? Response::HTTP_UNPROCESSABLE_ENTITY
            : Response::HTTP_OK;

        return $this->render('about/client/_form_modal_content.html.twig', [
            'form' => $form->createView(),
            'title' => $this->translator->trans('client.modal.title', [], 'messages'),
            'submit_label' => 'global.save',
        ], new Response(null, $status));
    }
}
