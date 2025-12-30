<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\App;

return App::config([
    'twig' => [
        'default_path' => __DIR__.'/../../templates',
        'form_themes' => ['bootstrap_5_layout.html.twig', 'form/layout.html.twig'],
    ],
    'when@test' => [
        'twig' => [
            'strict_variables' => true,
        ],
    ],
]);
