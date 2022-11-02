<?php

namespace App\NewsAggregator;

use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

final class NewsProvider implements NewsProviderInterface
{
    /**
     * @var NewsProviderInterface[]
     */
    private readonly iterable $providers;

    public function __construct(
        #[TaggedIterator(NewsProviderInterface::class, exclude: self::class)]
        iterable $providers
    ) {
        $this->providers = $providers;
    }

    public function retrieve(): array
    {
        $articles = [];

        foreach ($this->providers as $provider) {
            $articles[] = $provider->retrieve();
        }

        return array_merge(...$articles);
    }
}
