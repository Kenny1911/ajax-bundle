<?php

declare(strict_types=1);

namespace Kenny1911\AjaxBundle\Tests\EventListener;

use Kenny1911\AjaxBundle\Attribute\Ajax;
use Kenny1911\AjaxBundle\EventListener\AjaxRequestSubscriber;
use Kenny1911\AjaxBundle\Tests\Serializer\Normalizer\ProblemNormalizer;
use Kenny1911\AjaxBundle\Util\AjaxResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

final class AjaxRequestSubscriberTest extends TestCase
{
    private EventDispatcher $dispatcher;

    protected function setUp(): void
    {
        $subscriber = new AjaxRequestSubscriber(
            ajaxResolver: new AjaxResolver(),
            serializer: new Serializer(
                normalizers: [
                    new ProblemNormalizer(),
                    new PropertyNormalizer(
                        new ClassMetadataFactory(
                            new AttributeLoader(),
                        ),
                    ),
                ],
                encoders: [
                    new JsonEncoder(),
                ],
            ),
        );

        $this->dispatcher = new EventDispatcher();
        $this->dispatcher->addSubscriber($subscriber);
    }

    public function testOnControllerResolve(): void
    {
        $controller = new class {
            #[Ajax]
            public function action(): void {}
        };
        $request = new Request();
        $event = new ControllerEvent($this->createKernel(), [$controller, 'action'], $request, null);

        $this->dispatcher->dispatch($event, KernelEvents::CONTROLLER);

        self::assertTrue($request->attributes->has('_ajax'));
    }

    public function testOnControllerResolveControllerIsNotAjax(): void
    {
        $request = new Request();
        $event = new ControllerEvent($this->createKernel(), static fn() => null, $request, null);

        $this->dispatcher->dispatch($event, KernelEvents::CONTROLLER);

        self::assertFalse($request->attributes->has('_ajax'));
    }

    public function testOnView(): void
    {
        $request = new Request();
        $request->attributes->set('_ajax', new Ajax());
        $event = new ViewEvent(
            $this->createKernel(),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            new DataResponse('Foo', 'Bar'),
        );

        $this->dispatcher->dispatch($event, KernelEvents::VIEW);

        $response = $event->getResponse();
        self::assertInstanceOf(Response::class, $response);
        self::assertSame(200, $response->getStatusCode());
        self::assertSame('{"foo":"Foo","bar":"Bar"}', $response->getContent());
    }

    public function testOnControllerResolveWithStatusCodeANDSerializerGroups(): void
    {
        $request = new Request();
        $request->attributes->set('_ajax', new Ajax(statusCode: 204, serializationContext: ['groups' => 'foo']));
        $event = new ViewEvent(
            $this->createKernel(),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            new DataResponse('Foo', 'Bar'),
        );

        $this->dispatcher->dispatch($event, KernelEvents::VIEW);

        $response = $event->getResponse();
        self::assertInstanceOf(Response::class, $response);
        self::assertSame(204, $response->getStatusCode());
        self::assertSame('{"foo":"Foo"}', $response->getContent());
    }

    public function testOnViewControllerIsNotAjax(): void
    {
        $event = new ViewEvent(
            $this->createKernel(),
            new Request(),
            HttpKernelInterface::MAIN_REQUEST,
            'Controller Result',
        );

        $this->dispatcher->dispatch($event, KernelEvents::VIEW);

        self::assertNull($event->getResponse());
    }

    public function testOnException(): void
    {
        $request = new Request();
        $request->attributes->set('_ajax', new Ajax());
        $event = new ExceptionEvent(
            $this->createKernel(),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            new \Exception('Some error'),
        );

        $this->dispatcher->dispatch($event, KernelEvents::EXCEPTION);

        $response = $event->getResponse();
        self::assertInstanceOf(Response::class, $response);
        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertSame('{"error":"Some error","class":"Exception"}', $response->getContent());
    }

    public function testOnExceptionControllerIsNotAjax(): void
    {
        $event = new ExceptionEvent(
            $this->createKernel(),
            new Request(),
            HttpKernelInterface::MAIN_REQUEST,
            new \Exception(),
        );

        $this->dispatcher->dispatch($event, KernelEvents::EXCEPTION);

        self::assertNull($event->getResponse());
    }

    private function createKernel(): HttpKernelInterface
    {
        return new class implements HttpKernelInterface {
            public function handle(Request $request, int $type = self::MAIN_REQUEST, bool $catch = true): Response
            {
                throw new \LogicException('Test Stub HttpKernel can not handle request.');
            }
        };
    }
}
