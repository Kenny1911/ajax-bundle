<?php

declare(strict_types=1);

namespace Kenny1911\AjaxBundle\Tests\EventListener;

use Symfony\Component\Serializer\Attribute\Groups;

/**
 * @internal
 * @psalm-internal Kenny1911\AjaxBundle\Tests\EventListener
 */
final class DataResponse
{
    public function __construct(
        #[Groups(groups: 'foo')]
        public readonly string $foo,
        #[Groups(groups: 'bar')]
        public readonly string $bar,
    ) {}
}
