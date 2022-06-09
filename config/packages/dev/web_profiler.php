<?php

declare(strict_types=1);

use Symfony\Config\FrameworkConfig;
use Symfony\Config\WebProfilerConfig;

return static function (WebProfilerConfig $config, FrameworkConfig $frameworkConfig): void {
    $config
        ->toolbar(true)
        ->interceptRedirects(false);

    $frameworkConfig->profiler()->onlyExceptions(false);
};
