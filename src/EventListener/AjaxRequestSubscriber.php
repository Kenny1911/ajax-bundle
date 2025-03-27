<?php

declare(strict_types=1);

namespace Kenny1911\AjaxBundle\EventListener;

use Kenny1911\AjaxBundle\Attribute\Ajax;
use Kenny1911\AjaxBundle\Util\AjaxResolver;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

final class AjaxRequestSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly AjaxResolver $ajaxResolver,
        private readonly SerializerInterface $serializer,
        private readonly string $requestAttributeName = '_ajax',
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onControllerResolve',
            KernelEvents::VIEW => 'onView',
            KernelEvents::EXCEPTION => 'onException',
        ];
    }

    public function onControllerResolve(ControllerEvent $event): void
    {
        $ajax = $this->ajaxResolver->resolve($event->getController());

        if (null !== $ajax) {
            $event->getRequest()->attributes->set($this->requestAttributeName, $ajax);
        }
    }

    public function onView(ViewEvent $event): void
    {
        $ajax = $this->getRequestAjax($event->getRequest());

        if (null === $ajax) {
            return;
        }

        $json = $this->serializer->serialize(
            $event->getControllerResult(),
            'json',
            $ajax->serializationContext,
        );
        $response = JsonResponse::fromJsonString($json, $ajax->statusCode);

        $event->setResponse($response);
    }

    public function onException(ExceptionEvent $event): void
    {
        $ajax = $this->getRequestAjax($event->getRequest());

        if (null === $ajax) {
            return;
        }

        $throwable = $event->getThrowable();
        $flattenException = FlattenException::createFromThrowable($throwable);
        $json = $this->serializer->serialize($flattenException, 'json', ['exception' => $throwable/* 'title' => $throwable->getMessage() */]);
        $response = JsonResponse::fromJsonString($json);
        $response->setStatusCode($flattenException->getStatusCode());
        $event->setResponse($response);
    }

    private function getRequestAjax(Request $request): ?Ajax
    {
        /** @psalm-suppress MixedAssignment */
        $ajax = $request->attributes->get($this->requestAttributeName);

        return $ajax instanceof Ajax ? $ajax : null;
    }
}
