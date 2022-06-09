<?php

declare(strict_types=1);

use App\Security\User;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Config\SecurityConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (SecurityConfig $config, ContainerConfigurator $containerConfigurator): void {
    $config
        ->enableAuthenticatorManager(true);

    $config
        ->passwordHasher(PasswordAuthenticatedUserInterface::class)
        ->algorithm('auto');

    $config
        ->passwordHasher(User::class)
        ->algorithm('auto');

    $config->provider('admin')
        ->memory()
        ->user('admin')
        ->password(env('ADMIN_PASSWORD')->base64())
        ->roles(['ROLE_ADMIN']);

    $config->firewall('dev')
        ->pattern('^/(_(profiler|wdt)|css|images|js)/')
        ->security(false);

    $config->firewall('admin')
        ->lazy(true)
        ->provider('admin')
        ->httpBasic()
        ->realm('Secured Area');

    $config->accessControl()
        ->path('^/admin')
        ->roles('ROLE_ADMIN');

    if ('test' === $containerConfigurator->env()) {
        $config
            ->passwordHasher(PasswordAuthenticatedUserInterface::class)
            ->algorithm('auto')
            ->cost(4)
            ->timeCost(3)
            ->memoryCost(10);
    }
};
