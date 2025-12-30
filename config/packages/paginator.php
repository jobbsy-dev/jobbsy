<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\App;

return App::config([
    'knp_paginator' => [
        'template' => [
            'pagination' => 'default/pagination.html.twig',
        ],
    ],
]);
