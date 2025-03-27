<?php

declare(strict_types=1);

namespace Kenny1911\AjaxBundle\Util;

namespace Kenny1911\AjaxBundle\Util;

use Kenny1911\AjaxBundle\Attribute\Ajax;

/**
 * @internal
 * @psalm-internal Kenny1911\AjaxBundle
 */
final class AjaxResolver
{
    public function resolve(callable $controller): ?Ajax
    {
        if (\is_array($controller)) {
            return $this->getAjax(new \ReflectionMethod($controller[0], $controller[1]));
        }

        if (\is_string($controller) || $controller instanceof \Closure) {
            return $this->getAjax(new \ReflectionFunction($controller));
        }

        return $this->getAjax(new \ReflectionClass($controller))
            ?? $this->getAjax(new \ReflectionMethod($controller, '__invoke'));
    }

    private function getAjax(\ReflectionFunction|\ReflectionMethod|\ReflectionClass $reflector): ?Ajax
    {
        $attribute = $reflector->getAttributes(Ajax::class)[0] ?? null;
        $ajax = $attribute?->newInstance();

        return $ajax instanceof Ajax ? $ajax : null;
    }
}
