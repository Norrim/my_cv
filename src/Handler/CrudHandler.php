<?php

declare(strict_types=1);

namespace App\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Environment;

final class CrudHandler
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator,
        private readonly FormFactoryInterface $formFactory,
        private readonly RouterInterface $router,
        private readonly Environment $twig,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
    ) {}

    public function handleNew(
        Request $request,
        object $entity,
        string $formType,
        string $routeName,
        string $template,
        string $label
    ): Response {
        $form = $this->formFactory->create($formType, $entity, [
            'action' => $this->router->generate($routeName),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($entity);
            $this->em->flush();

            $request->getSession()->getFlashBag()->add('success', $this->translator->trans('crud.flash.created', ['%label%' => $label]));

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['reload' => true]);
            }

            return $this->redirectToResume();
        }

        $status = ($form->isSubmitted() && !$form->isValid())
            ? Response::HTTP_UNPROCESSABLE_ENTITY
            : Response::HTTP_OK;

        return new Response($this->twig->render($template, [
            'form' => $form->createView(),
            'title' => $this->translator->trans('crud.modal.new', ['%label%' => $label]),
            'submit_label' => 'global.save',
        ]), $status);
    }

    public function handleEdit(
        Request $request,
        object $entity,
        string $formType,
        string $routeName,
        string $template,
        string $label
    ): Response {
        // En PHP 8, on peut utiliser $entity->id si c'est public, ou s'assurer que l'objet a un getId()
        // Pour être propre, on pourrait utiliser PropertyAccessor ou vérifier la méthode
        $id = method_exists($entity, 'getId') ? $entity->getId() : null;

        $form = $this->formFactory->create($formType, $entity, [
            'action' => $this->router->generate($routeName, ['id' => $id]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $request->getSession()->getFlashBag()->add('success', $this->translator->trans('crud.flash.updated', ['%label%' => $label]));

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['reload' => true]);
            }

            return $this->redirectToResume();
        }

        $status = ($form->isSubmitted() && !$form->isValid())
            ? Response::HTTP_UNPROCESSABLE_ENTITY
            : Response::HTTP_OK;

        return new Response($this->twig->render($template, [
            'form' => $form->createView(),
            'title' => $this->translator->trans('crud.modal.edit', ['%label%' => $label]),
            'submit_label' => 'global.save',
        ]), $status);
    }

    public function handleDelete(
        Request $request,
        object $entity,
        string $tokenSlug,
        string $label
    ): Response {
        $id = method_exists($entity, 'getId') ? $entity->getId() : null;
        $tokenName = 'delete_' . $tokenSlug . '_' . $id;

        $token = (string)$request->request->get('_token');

        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken($tokenName, $token))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $this->em->remove($entity);
        $this->em->flush();

        $request->getSession()->getFlashBag()->add('success', $this->translator->trans('crud.flash.deleted', ['%label%' => $label]));

        return $this->redirectToResume();
    }

    private function createAccessDeniedException(string $message): AccessDeniedException
    {
        return new AccessDeniedException($message);
    }

    private function redirectToResume(): RedirectResponse
    {
        $url = $this->router->generate('app_home') . '#resume';
        return new RedirectResponse($url);
    }
}
