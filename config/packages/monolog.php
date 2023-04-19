<?php

declare(strict_types=1);

use Sentry\State\HubInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\MonologConfig;

return static function (MonologConfig $config, ContainerConfigurator $containerConfigurator): void {
    $config->channels(['deprecation']); // Deprecations are logged in the dedicated "deprecation" channel when it exists

    if ('dev' === $containerConfigurator->env()) {
        $config->handler('main')
            ->type('stream')
            ->path('%kernel.logs_dir%/%kernel.environment%.log')
            ->level('debug')
            ->channels()->elements(['!event']);

        $config->handler('console')
            ->type('console')
            ->processPsr3Messages(false)
            ->channels()->elements(['!event', '!doctrine', '!console']);
    }

    if ('prod' === $containerConfigurator->env()) {
        $mainHandler = $config->handler('main')
            ->type('fingers_crossed')
            ->actionLevel('error')
            ->handler('sentry')
            ->stopBuffering(false);

        $mainHandler->excludedHttpCode()->code(400);
        $mainHandler->excludedHttpCode()->code(403);
        $mainHandler->excludedHttpCode()->code(404);
        $mainHandler->excludedHttpCode()->code(405);

        $config->handler('sentry')
            ->type('sentry')
            ->level('error')
            ->hubId(HubInterface::class)
            ->fillExtraContext(true);

        $config->handler('console')
            ->type('console')
            ->processPsr3Messages(false)
            ->channels()->elements(['!event', '!doctrine']);

        $config->handler('deprecation')
            ->type('stream')
            ->path('php://stderr')
            ->channels()->elements(['deprecation']);
    }

    if ('test' === $containerConfigurator->env()) {
        $mainHandler = $config->handler('main');
        $mainHandler
            ->type('fingers_crossed')
            ->actionLevel('error')
            ->handler('nested')
            ->channels()->elements(['!event']);

        $mainHandler->excludedHttpCode()->code(404);
        $mainHandler->excludedHttpCode()->code(405);

        $config->handler('nested')
            ->type('stream')
            ->path('%kernel.logs_dir%/%kernel.environment%.log')
            ->level('debug');
    }
};
