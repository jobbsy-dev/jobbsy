<?php

declare(strict_types=1);

use App\Analytics\AnalyticsClient;
use App\Analytics\Dummy\DummyClient;
use App\Analytics\Plausible\PlausibleClient;
use Goutte\Client;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Clock\NativeClock;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autoconfigure()
        ->autowire();

    $services->load('App\\', '../src/')
        ->exclude([__DIR__.'/../src/DependencyInjection/', __DIR__.'/../src/Entity/', __DIR__.'/../src/Kernel.php']);

    $services->set(ClockInterface::class, NativeClock::class);
    $services->set(AnalyticsClient::class, PlausibleClient::class);

    if ('dev' === $containerConfigurator->env() || 'test' === $containerConfigurator->env()) {
        $services->set(ClockInterface::class, MockClock::class);
        $services->set(AnalyticsClient::class, DummyClient::class);
    }

    $services->set('goutte.client', Client::class)
        ->autowire();

    $services->alias(Client::class, 'goutte.client');
};
