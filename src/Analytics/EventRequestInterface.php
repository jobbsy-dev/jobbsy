<?php

namespace App\Analytics;

interface EventRequestInterface
{
    public function headers(): array;

    public function body(): array;
}
