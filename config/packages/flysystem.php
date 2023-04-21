<?php

declare(strict_types=1);

use AsyncAws\S3\S3Client;
use Symfony\Config\FlysystemConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (FlysystemConfig $config): void {
    $config->storage('media.storage.local')
        ->adapter('local')
        ->options([
            'directory' => '%kernel.project_dir%/public'
        ]);

    $config->storage('media.storage.aws')
        ->adapter('asyncaws')
        ->options([
            'client' => S3Client::class,
            'bucket' => 'jobbsy',
        ]);

    $config->storage('media.storage.memory')
        ->adapter('memory');

    $config->storage('media.storage')
        ->adapter('lazy')
        ->options([
            'source' => env('APP_MEDIA_SOURCE'),
        ]);
};
