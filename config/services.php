<?php

declare(strict_types=1);

use App\Analytics\AnalyticsClient;
use App\Analytics\Dummy\DummyClient;
use App\Analytics\Plausible\PlausibleClient;
use AsyncAws\S3\S3Client;
use League\Glide\Server;
use League\Glide\ServerFactory;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Clock\NativeClock;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

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

    $services->set(HttpBrowser::class)
        ->autowire();

    $services->set(S3Client::class)
        ->args([
            '$configuration' => [
                'accessKeyId' => env('AWS_ACCESS_KEY_ID'),
                'accessKeySecret' => env('AWS_ACCESS_KEY_SECRET'),
                'region' => 'eu-west-3',
            ]
        ]);

    $services->set(Server::class)
        ->factory([ServerFactory::class, 'create'])
        ->arg('$config', [
            'source' => service('media.storage'),
            'cache' => service('media.storage.memory'),
            'max_image_size' => 2000*2000,
        ]);
};
