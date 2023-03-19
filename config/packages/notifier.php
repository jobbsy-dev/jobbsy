<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\FrameworkConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (ContainerConfigurator $containerConfigurator, FrameworkConfig $framework): void {
    $containerConfigurator->extension('framework', [
        'notifier' => [
            'channel_policy' => [
                'urgent' => ['email'],
                'high' => ['email'],
                'medium' => ['email'],
                'low' => ['email'],
            ],
            'admin_recipients' => [[
                'email' => 'hello@jobbsy.dev',
            ]],
        ],
    ]);

    $framework->notifier()
        ->chatterTransport('slack', env('SLACK_DSN'))
    ;
};
