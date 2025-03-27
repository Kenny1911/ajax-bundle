<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Kenny1911\AjaxBundle\EventListener\AjaxRequestSubscriber;
use Kenny1911\AjaxBundle\Util\AjaxResolver;

return static function (ContainerConfigurator $di): void {
    $di->parameters()
        ->set('kenny1911_ajax_bundle.ajax_request_attribute', '_ajax');

    $di->services()
        ->set(AjaxRequestSubscriber::class)
            ->args([
                inline_service(AjaxResolver::class),
                service('serializer'),
                param('kenny1911_ajax_bundle.ajax_request_attribute'),
            ])
            ->tag('kernel.event_subscriber');
};
