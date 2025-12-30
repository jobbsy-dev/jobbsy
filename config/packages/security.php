<?php

declare(strict_types=1);

use App\Security\User;
use Symfony\Component\DependencyInjection\Loader\Configurator\App;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return App::config([
    'security' => [
        'password_hashers' => [
            PasswordAuthenticatedUserInterface::class => [
                'algorithm' => 'auto',
            ],
            User::class => [
                'algorithm' => 'auto',
            ],
        ],
        'providers' => [
            'admin' => [
                'memory' => [
                    'users' => [
                        'admin' => [
                            'password' => env('ADMIN_PASSWORD')->base64(),
                            'roles' => ['ROLE_ADMIN'],
                        ],
                    ],
                ],
            ],
        ],
        'firewalls' => [
            'dev' => [
                'pattern' => '^/(_(profiler|wdt)|css|images|js)/',
                'security' => false,
            ],
            'admin' => [
                'lazy' => true,
                'provider' => 'admin',
                'http_basic' => [
                    'realm' => 'Secured Area',
                ],
            ],
        ],
        'access_control' => [
            [
                'path' => '^/admin',
                'roles' => 'ROLE_ADMIN',
            ],
        ],
    ],
    'when@test' => [
        'security' => [
            'password_hashers' => [
                PasswordAuthenticatedUserInterface::class => [
                    'algorithm' => 'auto',
                    'cost' => 4,
                    'time_cost' => 3,
                    'memory_cost' => 10,
                ],
            ],
        ],
    ],
]);
