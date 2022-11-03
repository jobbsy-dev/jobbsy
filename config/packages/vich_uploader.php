<?php

declare(strict_types=1);

use Symfony\Config\VichUploaderConfig;
use Vich\UploaderBundle\Naming\OrignameNamer;

return static function (VichUploaderConfig $config): void {
    $config
        ->dbDriver('orm')
        ->storage('flysystem')
        ->metadata()->type('attribute');

    $config
        ->mappings('organization_image')
        ->uriPrefix('/images/organizations')
        ->uploadDestination('organization_image.storage')
        ->namer()->service(OrignameNamer::class);

    $config
        ->mappings('feed_image')
        ->uriPrefix('/images/feeds')
        ->uploadDestination('feed_image.storage')
        ->namer()->service(OrignameNamer::class);
};
