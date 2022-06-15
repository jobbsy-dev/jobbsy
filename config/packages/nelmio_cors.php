<?php

declare(strict_types=1);

use Symfony\Config\NelmioCorsConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (NelmioCorsConfig $config): void {
    $config
        ->defaults()
        ->originRegex(true)
        ->allowOrigin([env('CORS_ALLOW_ORIGIN')])
        ->allowMethods(['GET', 'OPTIONS', 'POST', 'PUT', 'PATCH', 'DELETE'])
        ->allowHeaders(['Content-Type', 'Authorization'])
        ->exposeHeaders(['Link'])
        ->maxAge(3600);

    $config->paths('^/', []);
};
