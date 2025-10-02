<?php

namespace App\News\Aggregator;

use App\Entity\News\Entry;
use App\Entity\News\Feed;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final readonly class FetchArticlesFromFeed
{
    /**
     * @param FetchArticlesFromFeedInterface[] $providers
     */
    public function __construct(
        #[AutowireIterator(FetchArticlesFromFeedInterface::class)]
        private iterable $providers,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @return Entry[]
     */
    public function __invoke(Feed $feed): array
    {
        $this->logger->info('Fetching articles from feed.', [
            'feed' => $feed->getName(),
            'feedUrl' => $feed->getUrl(),
        ]);

        foreach ($this->providers as $provider) {
            if (false === $provider->supports($feed)) {
                continue;
            }

            return ($provider)($feed);
        }

        return [];
    }
}
