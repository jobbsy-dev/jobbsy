<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\App;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return App::config([
    'doctrine' => [
        'dbal' => [
            'connections' => [
                'default' => [
                    'url' => env('DATABASE_URL')->resolve(),
                    'server_version' => '16',
                ],
            ],
        ],
        'orm' => [
            'default_entity_manager' => 'default',
            'entity_managers' => [
                'default' => [
                    'connection' => 'default',
                    'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware',
                    'auto_mapping' => true,
                    'mappings' => [
                        'App' => [
                            'is_bundle' => false,
                            'type' => 'attribute',
                            'dir' => '%kernel.project_dir%/src/Entity',
                            'prefix' => 'App\Entity',
                            'alias' => 'App',
                        ],
                    ],
                ],
            ],
        ],
    ],
    'when@prod' => [
        'doctrine' => [
            'orm' => [
                'entity_managers' => [
                    'default' => [
                        'metadata_cache_driver' => [
                            'type' => 'pool',
                            'pool' => 'doctrine.system_cache_pool',
                        ],
                        'query_cache_driver' => [
                            'type' => 'pool',
                            'pool' => 'doctrine.system_cache_pool',
                        ],
                        'result_cache_driver' => [
                            'type' => 'pool',
                            'pool' => 'doctrine.result_cache_pool',
                        ],
                    ],
                ],
            ],
        ],
        'framework' => [
            'cache' => [
                'pools' => [
                    'doctrine.result_cache_pool' => [
                        'adapters' => ['cache.app'],
                    ],
                    'doctrine.system_cache_pool' => [
                        'adapters' => ['cache.system'],
                    ],
                ],
            ],
        ],
    ],
    'when@test' => [
        'doctrine' => [
            'dbal' => [
                'connections' => [
                    'default' => [
                        'dbname_suffix' => '_test'.env('TEST_TOKEN')->default(''),
                    ],
                ],
            ],
        ],
    ],
]);
