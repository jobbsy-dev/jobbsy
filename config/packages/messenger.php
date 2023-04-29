<?php

declare(strict_types=1);

use App\Donation\Command\CreateDonationPaymentUrlCommand;
use App\Message\CreateTweetMessage;
use App\Message\Job\ClassifyMessage;
use App\Shared\AsyncMessageInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Config\FrameworkConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (FrameworkConfig $config, ContainerConfigurator $containerConfigurator): void {
    $messenger = $config->messenger();

    $messenger->failureTransport('failed');

    $messenger->transport('sync')->dsn('sync://');

    $messenger
        ->transport('async')
        ->dsn(env('MESSENGER_TRANSPORT_DSN'))
        ->retryStrategy()->maxRetries(3)->multiplier(2);

    $messenger
        ->transport('failed')
        ->dsn('doctrine://default?queue_name=failed');

    $messenger->routing(SendEmailMessage::class)->senders(['async']);
    $messenger->routing(ChatMessage::class)->senders(['async']);
    $messenger->routing(SmsMessage::class)->senders(['async']);
    $messenger->routing(CreateTweetMessage::class)->senders(['async']);
    $messenger->routing(CreateDonationPaymentUrlCommand::class)->senders(['sync']);
    $messenger->routing(AsyncMessageInterface::class)->senders(['async']);

    if ('test' === $containerConfigurator->env()) {
        $messenger
            ->transport('async')
            ->dsn('in-memory://');
    }
};
