<?php

declare(strict_types=1);

use Ramsey\Uuid\Doctrine\UuidType;
use Symfony\Config\DoctrineConfig;

return static function (DoctrineConfig $config): void {
    $config->dbal()
        ->type('uuid')
        ->class(UuidType::class);
};
