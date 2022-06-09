<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\FrameworkConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (FrameworkConfig $config, ContainerConfigurator $containerConfigurator): void {
    $config
        ->secret(env('APP_SECRET'))
        ->httpMethodOverride(false)
        ->phpErrors()
        ->log(true)
    ;

    $config->httpClient()
    ->scopedClient('mailjet.client')
    ->baseUri('https://api.mailjet.com/v3/REST/')
    ->authBasic(env('MAILJET_API_KEY').':'.env('MAILJET_API_SECRET_KEY'));

    $config->session()
        ->handlerId(null)
        ->cookieSecure('auto')
        ->cookieSamesite('lax')
        ->storageFactoryId('session.storage.factory.native');

    if ('test' === $containerConfigurator->env()) {
        $config
            ->test(true)
            ->session()
            ->storageFactoryId('session.storage.factory.mock_file');
    }
};
