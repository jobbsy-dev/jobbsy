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
        $options = $config->options();
        $options->integrations([IgnoreErrorsIntegration::class]);
        $config->registerErrorListener(false);

        $services = $containerConfigurator->services();

        $services->set(IgnoreErrorsIntegration::class)
            ->arg('$options', [
                'ignore_exceptions' => [
                    NotFoundHttpException::class,
                    AccessDeniedException::class,
                    MethodNotAllowedHttpException::class,
                    NotAcceptableHttpException::class,
                ]
            ]);
    }
};
