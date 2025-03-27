<?php

declare(strict_types=1);

namespace Kenny1911\AjaxBundle\Tests\Util;

use Kenny1911\AjaxBundle\Attribute\Ajax;
use Kenny1911\AjaxBundle\Util\AjaxResolver;
use PHPUnit\Framework\TestCase;

final class AjaxResolverTest extends TestCase
{
    public function testResolveObjectMethod(): void
    {
        $object = new class {
            #[Ajax(statusCode: 204, serializationContext: ['groups' => 'foo'])]
            public function action(): void {}
        };
        $resolver = new AjaxResolver();

        $ajax = $resolver->resolve([$object, 'action']);

        self::assertInstanceOf(Ajax::class, $ajax);
        self::assertSame(204, $ajax->statusCode);
        self::assertSame(['groups' => 'foo'], $ajax->serializationContext);
    }

    public function testResolveClassStaticMethod(): void
    {
        $object = new class {
            #[Ajax(statusCode: 204, serializationContext: ['groups' => 'foo'])]
            public static function action(): void {}
        };
        $resolver = new AjaxResolver();

        $ajax = $resolver->resolve([$object::class, 'action']);

        self::assertInstanceOf(Ajax::class, $ajax);
        self::assertSame(204, $ajax->statusCode);
        self::assertSame(['groups' => 'foo'], $ajax->serializationContext);
    }

    public function testResolveCallableObject(): void
    {
        $resolver = new AjaxResolver();

        $ajax = $resolver->resolve(new CallableController());

        self::assertInstanceOf(Ajax::class, $ajax);
        self::assertSame(204, $ajax->statusCode);
        self::assertSame(['groups' => 'foo'], $ajax->serializationContext);
    }

    public function testResolveCallableObjectInvokeMethod(): void
    {
        $resolver = new AjaxResolver();
        $object = new class {
            #[Ajax(statusCode: 204, serializationContext: ['groups' => 'foo'])]
            public function __invoke(): void {}
        };

        $ajax = $resolver->resolve($object);

        self::assertInstanceOf(Ajax::class, $ajax);
        self::assertSame(204, $ajax->statusCode);
        self::assertSame(['groups' => 'foo'], $ajax->serializationContext);
    }

    public function testResolverFunction(): void
    {
        $resolver = new AjaxResolver();

        $ajax = $resolver->resolve(__NAMESPACE__ . '\controller');

        self::assertInstanceOf(Ajax::class, $ajax);
        self::assertSame(204, $ajax->statusCode);
        self::assertSame(['groups' => 'foo'], $ajax->serializationContext);
    }

    public function testResolverClosure(): void
    {
        $resolver = new AjaxResolver();

        $ajax = $resolver->resolve(controller(...));

        self::assertInstanceOf(Ajax::class, $ajax);
        self::assertSame(204, $ajax->statusCode);
        self::assertSame(['groups' => 'foo'], $ajax->serializationContext);
    }

    public function testResolveNoAttribute(): void
    {
        $object = new class {
            public function action(): void {}
        };
        $resolver = new AjaxResolver();

        $ajax = $resolver->resolve([$object, 'action']);

        self::assertNull($ajax);
    }
}

#[Ajax(statusCode: 204, serializationContext: ['groups' => 'foo'])]
function controller(): void {}

/**
 * @internal
 * @psalm-internal Kenny1911\AjaxBundle\Tests\Util
 */
#[Ajax(statusCode: 204, serializationContext: ['groups' => 'foo'])]
final class CallableController
{
    public function __invoke(): void {}
}
