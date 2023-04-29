<?php

namespace App\Job;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag]
interface JobProviderInterface
{
    public function retrieve(SearchParameters $parameters): JobCollection;

    public function enabled(): bool;
}
