<?php

declare(strict_types=1);

use Symfony\Config\DoctrineMigrationsConfig;

return static function (DoctrineMigrationsConfig $config): void {
    $config
        ->migrationsPath('DoctrineMigrations', '%kernel.project_dir%/migrations')
        ->enableProfiler('%kernel.debug%');
};
