<?php

declare(strict_types=1);

use Monolog\Level;
use Sentry\Monolog\BreadcrumbHandler;
use Sentry\SentryBundle\Monolog\LogsHandler;
use Symfony\Component\DependencyInjection\Loader\Configurator\App;

return App::config([
    'services' => [
        LogsHandler::class => [
            'arguments' => [
                '$level' => Level::Info->value,
            ],
        ],
    ],
    'monolog' => [
        'channels' => ['deprecation'],
    ],
    'when@dev' => [
        'monolog' => [
            'handlers' => [
                'main' => [
                    'type' => 'stream',
                    'path' => '%kernel.logs_dir%/%kernel.environment%.log',
                    'level' => 'debug',
                    'channels' => ['!event'],
                ],
                'console' => [
                    'type' => 'console',
                    'process_psr_3_messages' => false,
                    'channels' => ['!event', '!doctrine', '!console'],
                ],
            ],
        ],
    ],
    'when@prod' => [
        'monolog' => [
            'handlers' => [
                'main' => [
                    'type' => 'fingers_crossed',
                    'action_level' => 'error',
                    'handler' => 'nested',
                    'buffer_size' => 50,
                    'excluded_http_code' => [400, 403, 404, 405, 406],
                ],
                'nested' => [
                    'type' => 'stream',
                    'path' => 'php://stderr',
                    'level' => 'debug',
                ],
                'sentry_breadcrumbs' => [
                    'type' => 'service',
                    'id' => BreadcrumbHandler::class,
                ],
                'sentry' => [
                    'type' => 'service',
                    'id' => LogsHandler::class,
                ],
                'console' => [
                    'type' => 'console',
                    'process_psr_3_messages' => false,
                    'channels' => ['!event', '!doctrine', '!console'],
                ],
                'deprecation' => [
                    'type' => 'stream',
                    'path' => 'php://stderr',
                    'channels' => ['deprecation'],
                ],
            ],
        ],
    ],
    'when@test' => [
        'monolog' => [
            'handlers' => [
                'main' => [
                    'type' => 'fingers_crossed',
                    'action_level' => 'error',
                    'handler' => 'nested',
                    'excluded_http_code' => [404, 405],
                    'channels' => ['!event'],
                ],
                'nested' => [
                    'type' => 'stream',
                    'path' => '%kernel.logs_dir%/%kernel.environment%.log',
                    'level' => 'debug',
                ],
            ],
        ],
    ],
]);
