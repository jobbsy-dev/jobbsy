<?php

declare(strict_types=1);

use App\Donation\Command\CreateDonationPaymentUrlCommand;
use Symfony\Component\DependencyInjection\Loader\Configurator\App;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Message\SmsMessage;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return App::config([
    'framework' => [
        'messenger' => [
            'failure_transport' => 'failed',
            'transports' => [
                'sync' => [
                    'dsn' => 'sync://',
                ],
                'async' => [
                    'dsn' => env('MESSENGER_TRANSPORT_DSN'),
                    'retry_strategy' => [
                        'max_retries' => 3,
                        'multiplier' => 2,
                    ],
                ],
                'failed' => [
                    'dsn' => 'doctrine://default?queue_name=failed',
                ],
            ],
            'routing' => [
                SendEmailMessage::class => ['async'],
                ChatMessage::class => ['async'],
                SmsMessage::class => ['async'],
                CreateDonationPaymentUrlCommand::class => ['sync'],
            ],
        ],
    ],
    'when@test' => [
        'framework' => [
            'messenger' => [
                'transports' => [
                    'async' => [
                        'dsn' => 'in-memory://',
                    ],
                ],
            ],
        ],
    ],
]);
