<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\FrameworkConfig;
use Symfony\Config\WebpackEncoreConfig;

return static function (WebpackEncoreConfig $webpackEncoreConfig, FrameworkConfig $frameworkConfig, ContainerConfigurator $containerConfigurator): void {
    $webpackEncoreConfig
        ->outputPath(__DIR__.'/../../public/build')
        ->scriptAttributes('defer', true)
        ->strictMode(true);

    $frameworkConfig
        ->assets()
        ->jsonManifestPath(__DIR__.'/../../public/build/manifest.json');

    if ('prod' === $containerConfigurator->env()) {
        $webpackEncoreConfig->cache(true);
    }

    if ('test' === $containerConfigurator->env()) {
        $webpackEncoreConfig->strictMode(false);
    }
};
