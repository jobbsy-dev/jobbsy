<?php

namespace App\NewsAggregator\Source;

final class SensiolabsProvider extends RSSProvider
{
    public const NAME = 'Sensiolabs';

    protected function getRSSUrl(): string
    {
        return 'https://blog.sensiolabs.com/category/symfony/feed/';
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
