<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (ContainerConfigurator $containerConfigurator): void {
    if ('prod' === $containerConfigurator->env()) {
        $containerConfigurator->extension('sentry', [
            'dsn' => env('SENTRY_DSN'),
            'register_error_listener' => false,
            'register_error_handler' => false,
            'options' => [
                'enable_logs' => true,
                'ignore_exceptions' => [
                    NotFoundHttpException::class,
                    BadRequestHttpException::class,
                    MethodNotAllowedHttpException::class,
                ],
            ]
        ]);
    }
};
