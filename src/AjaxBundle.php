<?php

declare(strict_types=1);

namespace Kenny1911\AjaxBundle;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AjaxBundle extends Bundle
{
    /**
     * @throws \Exception
     */
    public function build(ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/Resources/config'));
        $loader->load('services.php');
    }
}
