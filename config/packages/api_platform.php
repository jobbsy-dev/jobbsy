<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\App;

return App::config([
    'api_platform' => [
        'mapping' => [
            'paths' => [__DIR__.'/../../src/Entity'],
        ],
        'patch_formats' => [
            'json' => ['mime_types' => ['application/merge-patch+json']],
        ],
        'swagger' => [
            'versions' => [3],
        ],
        'show_webby' => false,
        'title' => 'Jobbsy API',
        'version' => '0.1.0',
    ],
]);
