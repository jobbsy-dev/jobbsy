<?php

namespace App\News;

use App\Entity\Article;
use App\Entity\Feed;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag]
interface FetchArticlesFromFeedInterface
{
    /**
     * @return Article[]
     */
    public function __invoke(Feed $feed): array;

    public function supports(Feed $feed): bool;
}
