<?php

declare(strict_types=1);

namespace Kenny1911\AjaxBundle\Tests\EventListener;

/**
 * @internal
 * @psalm-internal Kenny1911\AjaxBundle\Tests\EventListener
 */
final class DataResponse
{
    public function __construct(
        public readonly string $foo,
        public readonly string $bar,
    ) {}
}
