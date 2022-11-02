<?php

namespace App\NewsAggregator\Source;

final class SymfonyCastsProvider extends RSSProvider
{
    protected function getRSSUrl(): string
    {
        return 'https://feeds.feedburner.com/knpuniversity';
    }
}
