<?php

declare(strict_types=1);

namespace Kenny1911\AjaxBundle\Attribute;

use Symfony\Component\HttpFoundation\Response;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_FUNCTION)]
final class Ajax
{
    /**
     * @param array<string, mixed> $serializationContext
     */
    public function __construct(
        public readonly int $statusCode = Response::HTTP_OK,
        public readonly array $serializationContext = [],
    ) {}
}
