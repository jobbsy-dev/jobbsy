<?php

declare(strict_types=1);

use Symfony\Config\SentryConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (SentryConfig $config): void {
    $config->dsn(env('SENTRY_DSN'));
    $config->registerErrorListener(false);
    $config->registerErrorHandler(false);
};
