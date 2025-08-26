<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\DoctrineConfig;
use Symfony\Config\FrameworkConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (DoctrineConfig $config, FrameworkConfig $frameworkConfig, ContainerConfigurator $containerConfigurator): void {
    $config->dbal()
        ->connection('default')
        ->url(env('DATABASE_URL')->resolve())
        ->serverVersion('16')
        ->useSavepoints(true);

    $config->orm()
        ->autoGenerateProxyClasses(true)
        ->enableLazyGhostObjects(true)  ;

    $emDefault = $config->orm()->entityManager('default');

    $emDefault
        ->connection('default')
        ->namingStrategy('doctrine.orm.naming_strategy.underscore_number_aware')
        ->autoMapping(true)
        ->reportFieldsWhereDeclared(true);

    $emDefault->mapping('App')
        ->isBundle(false)
        ->type('attribute')
        ->dir('%kernel.project_dir%/src/Entity')
        ->prefix('App\Entity')
        ->alias('App');

    if ('prod' === $containerConfigurator->env()) {
        $emDefault = $config->orm()->entityManager('default');
        $config->orm()->autoGenerateProxyClasses(false);
        $emDefault->queryCacheDriver()
            ->type('pool')
            ->pool('doctrine.system_cache_pool');
        $emDefault->resultCacheDriver()
            ->type('pool')
            ->pool('doctrine.result_cache_pool');

        $cache = $frameworkConfig->cache();

        $cache->pool('doctrine.result_cache_pool')
            ->adapters(['cache.app']);

        $cache->pool('doctrine.system_cache_pool')
            ->adapters(['cache.system']);
    }

    if ('test' === $containerConfigurator->env()) {
        $config
            ->dbal()
            ->connection('default')
            ->dbnameSuffix('_test'. env('TEST_TOKEN')->default(''));
    }
};
