<?php

namespace App\Provider;

use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

final readonly class JobProvider implements JobProviderInterface
{
    /**
     * @var JobProviderInterface[]
     */
    private iterable $providers;

    public function __construct(
        #[TaggedIterator(JobProviderInterface::class, exclude: self::class)]
        iterable $providers
    ) {
        $this->providers = $providers;
    }

    public function retrieve(SearchParameters $parameters): JobCollection
    {
        $jobs = new JobCollection();

        foreach ($this->providers as $provider) {
            if (false === $provider->enabled()) {
                continue;
            }

            $jobs->addJob(...$provider->retrieve($parameters)->all());
        }

        return $jobs;
    }

    public function enabled(): bool
    {
        return true;
    }
}
