<?php

declare(strict_types=1);

use AsyncAws\S3\S3Client;
use Symfony\Component\DependencyInjection\Loader\Configurator\App;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return App::config([
    'flysystem' => [
        'storages' => [
            'media.storage.local' => [
                'adapter' => 'local',
                'options' => [
                    'directory' => '%kernel.project_dir%/public',
                ],
            ],
            'media.storage.aws' => [
                'adapter' => 'asyncaws',
                'options' => [
                    'client' => S3Client::class,
                    'bucket' => 'jobbsy',
                ],
            ],
            'media.storage.memory' => [
                'adapter' => 'memory',
            ],
            'media.storage' => [
                'adapter' => 'lazy',
                'options' => [
                    'source' => env('APP_MEDIA_SOURCE'),
                ],
            ],
        ],
    ],
]);
