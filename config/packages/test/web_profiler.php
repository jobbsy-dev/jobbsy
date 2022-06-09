<?php

declare(strict_types=1);

use Symfony\Config\FrameworkConfig;
use Symfony\Config\WebProfilerConfig;

return static function (WebProfilerConfig $config, FrameworkConfig $frameworkConfig): void {
    $config
        ->toolbar(false)
        ->interceptRedirects(false);

    $frameworkConfig->profiler()->collect(false);
};
