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
        #[Autowire('%env(CONTACT_EMAIL)%')] string $contactEmail
    ): Response {
        $contactRequest = new ContactRequest();

        $form = $this->createForm(ContactType::class, $contactRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = (new Email())
                ->from($contactRequest->email)
                ->to($contactEmail)
                ->subject(sprintf('New message from %s', $contactRequest->name))
                ->text($contactRequest->message)
                ->replyTo($contactRequest->email);

            $mailer->send($email);

            $this->addFlash('success', 'Thanks! Your message has been sent.');

            return $this->redirectToRoute('app_home', ['_fragment' => 'contact']);
        }

        $this->addFlash('error', 'Please check the form for errors.');

        return $this->redirectToRoute('app_home', ['_fragment' => 'contact']);
    }
}
