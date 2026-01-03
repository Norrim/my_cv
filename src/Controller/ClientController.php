<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use App\Service\FileUploader;
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
        private readonly FileUploader $fileUploader,
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
                        $filePath = $this->fileUploader->getTargetDirectory() . '/' . str_replace('assets/images/clients/', '', $existingClient->getUrl());
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
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
                        $oldFilePath = $this->fileUploader->getTargetDirectory() . '/' . str_replace('assets/images/clients/', '', $client->getUrl());
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                        }
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

    #[Route('/new', name: 'app_client_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
    ): Response {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $logoFile = $form->get('logo')->getData();
            if ($logoFile) {
                $logoFileName = $this->fileUploader->upload($logoFile);
                $client->setUrl('assets/images/clients/' . $logoFileName);
            }

            $this->clientRepository->save($client, true);

            $this->addFlash('success', 'Client ajouté avec succès !');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('about/client/new.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }
}
