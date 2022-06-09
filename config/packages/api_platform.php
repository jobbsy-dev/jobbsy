<?php

declare(strict_types=1);

use Symfony\Config\ApiPlatformConfig;

return static function (ApiPlatformConfig $apiPlatformConfig): void {
    $apiPlatformConfig
        ->mapping([
            'paths' => [__DIR__.'/../../src/Entity']
        ])
        ->paths([__DIR__.'/../../src/Entity']);

    $apiPlatformConfig->patchFormats('json', ['mime_types' => ['application/merge-patch+json']]);
    $apiPlatformConfig->swagger([
        'versions' => [3]
    ]);
    $apiPlatformConfig->showWebby(false);
    $apiPlatformConfig->title('Jobbsy API');
    $apiPlatformConfig->version('0.1.0');
};
