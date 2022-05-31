<?php

namespace App\Provider;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure]
interface JobProviderInterface
{
    public function retrieve(SearchParameters $parameters): JobCollection;

    public function enabled(): bool;
}
