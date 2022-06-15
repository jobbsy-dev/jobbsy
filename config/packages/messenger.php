<?php

declare(strict_types=1);

use App\Message\CreateTweetMessage;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Config\FrameworkConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (FrameworkConfig $config, ContainerConfigurator $containerConfigurator): void {
    $config->messenger()
        ->failureTransport('failed');

    $config->messenger()
        ->transport('async')
        ->dsn(env('MESSENGER_TRANSPORT_DSN'))
        ->retryStrategy()->maxRetries(3)->multiplier(2);

    $config->messenger()
        ->transport('failed')
        ->dsn('doctrine://default?queue_name=failed');

    $config->messenger()->routing(SendEmailMessage::class)->senders(['async']);
    $config->messenger()->routing(ChatMessage::class)->senders(['async']);
    $config->messenger()->routing(SmsMessage::class)->senders(['async']);
    $config->messenger()->routing(CreateTweetMessage::class)->senders(['async']);

    if ('test' === $containerConfigurator->env()) {
        $config->messenger()
            ->transport('async')
            ->dsn('in-memory://');
    }
};
