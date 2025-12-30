<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\App;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return App::config([
    'framework' => [
        'secret' => env('APP_SECRET'),
        'http_method_override' => false,
        'php_errors' => [
            'log' => true,
        ],
        'session' => [
            'gc_probability' => 0,
            'handler_id' => null,
            'cookie_secure' => 'auto',
            'cookie_samesite' => 'lax',
            'storage_factory_id' => 'session.storage.factory.native',
        ],
        'http_client' => [
            'scoped_clients' => [
                'mailjet.client' => [
                    'base_uri' => 'https://api.mailjet.com/v3/REST/',
                    'auth_basic' => sprintf('%s:%s', env('MAILJET_API_KEY'), env('MAILJET_API_SECRET_KEY')),
                ],
                'openai.client' => [
                    'base_uri' => 'https://api.openai.com/v1/',
                    'auth_bearer' => env('OPENAI_API_KEY'),
                ],
                'github.client' => [
                    'base_uri' => 'https://api.github.com',
                    'auth_bearer' => env('GITHUB_API_TOKEN'),
                ],
            ],
        ],
        'html_sanitizer' => [
            'sanitizers' => [
                'app.article_sanitizer' => [
                    'block_elements' => ['a', 'ul', 'li', 'p'],
                    'drop_elements' => ['figure', 'img', 'hr'],
                ],
            ],
        ],
        'trusted_proxies' => env('TRUSTED_PROXIES'),
        'trusted_headers' => ['x-forwarded-for', 'x-forwarded-proto'],
    ],
    'when@test' => [
        'framework' => [
            'test' => true,
            'session' => [
                'storage_factory_id' => 'session.storage.factory.mock_file',
            ],
        ],
    ],
]);
