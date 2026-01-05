<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\ContactRequest;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact_submit', methods: ['POST'])]
    public function submit(
        Request $request,
        MailerInterface $mailer,
        #[Autowire('%env(CONTACT_EMAIL)%')] string $contactEmail,
        #[Autowire('%env(CONTACT_FROM_EMAIL)%')] string $fromEmail,
    ): Response {
        $contactRequest = new ContactRequest();

        $form = $this->createForm(ContactType::class, $contactRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', $form->getErrors(true)->current()->getMessage());
            return $this->redirectToRoute('app_home', ['_fragment' => 'contact']);
        }

        if (!$form->isSubmitted()) {
            return $this->redirectToRoute('app_home', ['_fragment' => 'contact']);
        }

        $email = (new Email())
            ->from($fromEmail)
            ->to($contactEmail)
            ->replyTo((string) $contactRequest->email)
            ->subject(sprintf('New message from %s', (string) $contactRequest->name))
            ->text((string) $contactRequest->message);

        $mailer->send($email);

        $this->addFlash('success', 'Thanks! Your message has been sent.');

        return $this->redirectToRoute('app_home', ['_fragment' => 'contact']);
    }

}
