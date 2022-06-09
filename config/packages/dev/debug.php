<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Forwards VarDumper Data clones to a centralized server allowing to inspect dumps on CLI or in your browser.
    // See the "server:dump" command to start a new server.
    $containerConfigurator->extension('debug', [
        'dump_destination' => 'tcp://%env(VAR_DUMPER_SERVER)%'
    ]);
};
