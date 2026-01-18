<?php

declare(strict_types=1);

namespace App\Tests\Handler;

use App\Handler\CrudHandler;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class CrudHandlerTest extends TestCase
{
    private EntityManagerInterface $em;
    private TranslatorInterface $translator;
    private FormFactoryInterface $formFactory;
    private RouterInterface $router;
    private Environment $twig;
    private CsrfTokenManagerInterface $csrfTokenManager;
    private CrudHandler $handler;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->router = $this->createMock(RouterInterface::class);
        $this->twig = $this->createMock(Environment::class);
        $this->csrfTokenManager = $this->createMock(CsrfTokenManagerInterface::class);

        $this->handler = new CrudHandler(
            $this->em,
            $this->translator,
            $this->formFactory,
            $this->router,
            $this->twig,
            $this->csrfTokenManager
        );
    }

    public function testHandleNewSuccess(): void
    {
        $request = new Request();
        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);

        $entity = new \stdClass();
        $formType = 'App\Form\SomeType';
        $routeName = 'app_home';
        $template = 'template.html.twig';
        $label = 'Test';

        $form = $this->createMock(FormInterface::class);
        $this->formFactory->method('create')->willReturn($form);

        $form->expects($this->once())->method('handleRequest')->with($request);
        $form->method('isSubmitted')->willReturn(true);
        $form->method('isValid')->willReturn(true);

        $this->em->expects($this->once())->method('persist')->with($entity);
        $this->em->expects($this->once())->method('flush');

        $this->router->method('generate')->willReturn('/home');

        $response = $this->handler->handleNew($request, $entity, $formType, $routeName, $template, $label);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringContainsString('/home#resume', $response->getTargetUrl());
    }

    public function testHandleNewAjaxSuccess(): void
    {
        $request = new Request();
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);

        $entity = new \stdClass();
        $form = $this->createMock(FormInterface::class);
        $this->formFactory->method('create')->willReturn($form);
        $form->method('isSubmitted')->willReturn(true);
        $form->method('isValid')->willReturn(true);

        $response = $this->handler->handleNew($request, $entity, 'Type', 'route', 'tpl', 'label');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame('{"reload":true}', $response->getContent());
    }

    public function testHandleDeleteSuccess(): void
    {
        $request = new Request([], ['_token' => 'valid_token']);
        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);

        $entity = $this->createMock(DummyEntity::class);
        $entity->method('getId')->willReturn(1);

        $this->csrfTokenManager->method('isTokenValid')->willReturn(true);

        $this->em->expects($this->once())->method('remove')->with($entity);
        $this->em->expects($this->once())->method('flush');

        $this->router->method('generate')->willReturn('/home');

        $response = $this->handler->handleDelete($request, $entity, 'test', 'Label');

        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    public function testHandleDeleteInvalidToken(): void
    {
        $request = new Request([], ['_token' => 'invalid_token']);
        $entity = $this->createMock(DummyEntity::class);
        $entity->method('getId')->willReturn(1);

        $this->csrfTokenManager->method('isTokenValid')->willReturn(false);

        $this->expectException(\Symfony\Component\Security\Core\Exception\AccessDeniedException::class);

        $this->handler->handleDelete($request, $entity, 'test', 'Label');
    }
}

class DummyEntity
{
    public function getId(): int
    {
        return 1;
    }
}
