<?php

declare(strict_types=1);

use Sentry\Integration\IgnoreErrorsIntegration;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Config\SentryConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (SentryConfig $config, ContainerConfigurator $containerConfigurator): void {
    if ('prod' === $containerConfigurator->env()) {
        $config->dsn(env('SENTRY_DSN'));
        $config->registerErrorListener(false);
    }
};
