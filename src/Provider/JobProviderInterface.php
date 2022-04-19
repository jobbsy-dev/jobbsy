<?php

namespace App\Provider;

interface JobProviderInterface
{
    public function retrieve(SearchParameters $parameters): JobCollection;

    public function enabled(): bool;
}
