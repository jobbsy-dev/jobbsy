<?php

namespace App\Analytics;

interface AnalyticsClient
{
    public function event(EventRequestInterface $request): void;
}
