<?php

namespace App\NewsAggregator\Source;

final class SymfonyProvider extends RSSProvider
{
    protected function getRSSUrl(): string
    {
        return 'https://feeds.feedburner.com/symfony/blog';
    }
}
