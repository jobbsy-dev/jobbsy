<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\App;

return App::config([
    'framework' => [
        'asset_mapper' => [
            'paths' => ['assets/'],
        ],
    ],
]);
