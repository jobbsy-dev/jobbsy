<?php

declare(strict_types=1);

use Symfony\Config\FlysystemConfig;

return static function (FlysystemConfig $config): void {
    $config->storage('organization_image.storage')
        ->adapter('local')
        ->options(['directory' => '%kernel.project_dir%/public/images/organizations']);
};
