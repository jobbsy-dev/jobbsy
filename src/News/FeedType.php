<?php

namespace App\News;

enum FeedType: string
{
    case RSS = 'rss';
    case ATOM = 'atom';
}
