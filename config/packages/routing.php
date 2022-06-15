<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $config, ContainerConfigurator $containerConfigurator): void {
    $config->router()->utf8(true);

    if ('prod' === $containerConfigurator->env()) {
        $config->router()->strictRequirements(null);
    }
};
