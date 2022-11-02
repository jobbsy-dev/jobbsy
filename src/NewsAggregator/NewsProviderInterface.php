<?php

namespace App\NewsAggregator;

use App\Entity\Article;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag]
interface NewsProviderInterface
{
    /**
     * @return Article[]
     */
    public function retrieve(): array;
}
