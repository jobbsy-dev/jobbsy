<?php

declare(strict_types=1);

use Ramsey\Uuid\Doctrine\UuidType;
use Symfony\Component\DependencyInjection\Loader\Configurator\App;

return App::config([
    'doctrine' => [
        'dbal' => [
            'types' => [
                'uuid' => UuidType::class,
            ],
        ],
    ],
]);
