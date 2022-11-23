<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\FrameworkConfig;
use Symfony\Config\WebProfilerConfig;

return static function (WebProfilerConfig $config, FrameworkConfig $frameworkConfig, ContainerConfigurator $containerConfigurator): void {
    if ($containerConfigurator->env() === 'dev') {
        $config
            ->toolbar(true)
            ->interceptRedirects(false);

        $frameworkConfig->profiler()
            ->onlyExceptions(false)
            ->collectSerializerData(true);
    }

    if ($containerConfigurator->env() === 'test') {
        $config
            ->toolbar(false)
            ->interceptRedirects(false);

        $containerConfigurator->extension('framework', [
            'profiler' => [
                'collect' => false,
            ],
        ]);
        $frameworkConfig->profiler()
            ->collect(false);
    }
};
