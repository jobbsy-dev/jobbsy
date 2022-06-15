<?php

declare(strict_types=1);

use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $config): void {
    $config
        ->defaultLocale('en')
        ->translator()
        ->defaultPath(__DIR__.'/../../translations')
        ->fallbacks('en');
};
