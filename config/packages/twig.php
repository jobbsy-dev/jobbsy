<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\TwigConfig;

return static function (TwigConfig $config, ContainerConfigurator $containerConfigurator): void {
    $config
        ->defaultPath(__DIR__.'/../../templates')
        ->formThemes(['bootstrap_5_layout.html.twig', 'form/layout.html.twig']);

    if ('test' === $containerConfigurator->env()) {
        $config->strictVariables(true);
    }
};
