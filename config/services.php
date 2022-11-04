<?php

declare(strict_types=1);

use App\Analytics\AnalyticsClient;
use App\Analytics\Dummy\DummyClient;
use App\Analytics\Plausible\PlausibleClient;
use App\Clock\MockClock;
use App\Clock\SystemClock;
use StellaMaris\Clock\ClockInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->parameters()
        ->set('app_version', env('APP_VERSION'));

    $services = $containerConfigurator->services()
        ->defaults()
        ->autoconfigure()
        ->autowire();

    $services->load('App\\', '../src/')
        ->exclude([
            '../src/DependencyInjection/',
            '../src/Entity/',
            '../src/Kernel.php'
        ]);

    $services->set(ClockInterface::class, SystemClock::class);
    $services->set(AnalyticsClient::class, PlausibleClient::class);

    if ('dev' === $containerConfigurator->env() || 'test' === $containerConfigurator->env()) {
        $services->set(ClockInterface::class, MockClock::class);
        $services->set(AnalyticsClient::class, DummyClient::class);
    }
};
