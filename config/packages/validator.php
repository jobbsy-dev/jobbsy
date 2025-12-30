<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\App;

return App::config([
    'framework' => [
        'validation' => [
            'email_validation_mode' => 'html5',
            'not_compromised_password' => [
                'enabled' => false,
            ],
        ],
    ],
]);
