<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', ['notifier' => ['channel_policy' => ['urgent' => ['email'], 'high' => ['email'], 'medium' => ['email'], 'low' => ['email']], 'admin_recipients' => [['email' => 'admin@example.com']]]]);
};
