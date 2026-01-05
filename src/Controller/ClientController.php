<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use App\Service\ClientFileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/client')]
final class ClientController extends AbstractController
{
    public function __construct(
        private readonly ClientRepository $clientRepository,
        private readonly EntityManagerInterface $em,
        private readonly ClientFileUploader $fileUploader,
        private readonly TranslatorInterface $translator,
    ) {}

    #[Route('/edit-all', name: 'client_edit_all', methods: ['GET', 'POST'])]
    public function editAll(Request $request): Response
    {
        $clients = $this->clientRepository->findBy([], ['position' => 'ASC']);

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
            $existingClients = $this->clientRepository->findAll();
            $submittedClients = $form->get('clients')->getData();

            // Remove clients that are no longer in the submitted list
            foreach ($existingClients as $existingClient) {
                if (!in_array($existingClient, $submittedClients, true)) {
                    // Delete the physical file before removing from DB
                    if ($existingClient->getUrl()) {
                        $this->fileUploader->remove(basename($existingClient->getUrl()));
                    }
                    $this->em->remove($existingClient);
                }
            }

            foreach ($submittedClients as $key => $client) {
                $clientForm = $form->get('clients')->get((string) $key);
                $logoFile = $clientForm->get('logo')->getData();

                if ($logoFile) {
                    // Delete old file if exists
                    if ($client->getUrl()) {
                        $this->fileUploader->remove(basename($client->getUrl()));
                    }

                    $logoFileName = $this->fileUploader->upload($logoFile);
                    $client->setUrl('assets/images/clients/' . $logoFileName);
                }

                $this->em->persist($client);
            }
            $this->em->flush();

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
