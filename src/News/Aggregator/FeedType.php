<?php

namespace App\News\Aggregator;

enum FeedType: string
{
    case RSS = 'rss';
    case ATOM = 'atom';
}
