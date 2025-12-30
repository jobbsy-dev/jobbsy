<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\App;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return App::config([
    'framework' => [
        'notifier' => [
            'channel_policy' => [
                'urgent' => ['email'],
                'high' => ['email'],
                'medium' => ['email'],
                'low' => ['email'],
            ],
            'admin_recipients' => [['email' => 'hello@jobbsy.dev']],
            'chatter_transports' => [
                'slack' => env('SLACK_DSN'),
            ],
        ],
    ],
]);
