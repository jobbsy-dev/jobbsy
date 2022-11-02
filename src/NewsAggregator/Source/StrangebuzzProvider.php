<?php

namespace App\NewsAggregator\Source;

final class StrangebuzzProvider extends AtomProvider
{
    protected function getFeedUrl(): string
    {
        return 'https://feeds.feedburner.com/strangebuzz/en';
    }
}
