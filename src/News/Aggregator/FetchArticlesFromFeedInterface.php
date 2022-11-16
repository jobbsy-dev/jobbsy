<?php

namespace App\News\Aggregator;

use App\Entity\News\Entry;
use App\Entity\News\Feed;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag]
interface FetchArticlesFromFeedInterface
{
    /**
     * @return Entry[]
     */
    public function __invoke(Feed $feed): array;

    public function supports(Feed $feed): bool;
}
