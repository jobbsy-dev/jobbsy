<?php

namespace App\News\Aggregator;

use App\Entity\News\Feed;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

final class FetchArticlesFromFeed
{
    /**
     * @var FetchArticlesFromFeedInterface[]
     */
    private readonly iterable $providers;

    public function __construct(
        #[TaggedIterator(FetchArticlesFromFeedInterface::class)]
        iterable $providers
    ) {
        $this->providers = $providers;
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
