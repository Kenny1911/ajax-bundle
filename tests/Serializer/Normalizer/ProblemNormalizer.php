<?php

declare(strict_types=1);

namespace Kenny1911\AjaxBundle\Tests\Serializer\Normalizer;

use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Special {@see FlattenException} normalizer for tests.
 *
 * @internal
 * @psalm-internal Kenny1911\AjaxBundle\Tests
 */
final class ProblemNormalizer implements NormalizerInterface
{
    public function normalize(mixed $data, ?string $format = null, array $context = []): null|array|string|int|float|bool|\ArrayObject
    {
        if (false === $data instanceof FlattenException) {
            throw new InvalidArgumentException(\sprintf('Invalid data type. Expected %s, actual %s.', FlattenException::class, get_debug_type($data)));
        }

        return [
            'error' => $data->getMessage(),
            'class' => $data->getClass(),
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof FlattenException;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            FlattenException::class => true,
        ];
    }
}
