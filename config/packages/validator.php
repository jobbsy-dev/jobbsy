<?php

declare(strict_types=1);

use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $config): void {
    $config->validation()->emailValidationMode('html5');
    $config->validation()->notCompromisedPassword()->enabled(false);
};
