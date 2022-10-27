<?php

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CountryEmojiExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('country_emoji', [$this, 'countryEmoji']),
        ];
    }

    public function countryEmoji(string $countryCode): string
    {
        $regionalOffset = 0x1F1A5;

        return mb_chr($regionalOffset + mb_ord($countryCode[0], 'UTF-8'), 'UTF-8')
            .mb_chr($regionalOffset + mb_ord($countryCode[1], 'UTF-8'), 'UTF-8');
    }
}
