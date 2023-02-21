<?php

namespace App\News\Aggregator;

use App\Entity\News\Feed;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

final readonly class FetchArticlesFromFeed
{
    /**
     * @param FetchArticlesFromFeedInterface[] $providers
     */
    public function __construct(#[TaggedIterator(FetchArticlesFromFeedInterface::class)] private iterable $providers)
    {
    }

    public function __invoke(Feed $feed): array
    {
        foreach ($this->providers as $provider) {
            if (false === $provider->supports($feed)) {
                continue;
            }

            return ($provider)($feed);
        }

        return [];
    }
}
