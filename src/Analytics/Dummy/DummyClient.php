<?php

namespace App\Analytics\Dummy;

use App\Analytics\AnalyticsClient;
use App\Analytics\EventRequestInterface;

final class DummyClient implements AnalyticsClient
{
    public function event(EventRequestInterface $request): void
    {
    }
}
