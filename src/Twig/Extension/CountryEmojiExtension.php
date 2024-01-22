<?php

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class CountryEmojiExtension extends AbstractExtension
{
    /**
     * @var int
     */
    private const int REGIONAL_OFFSET = 0x1F1A5;

    public function getFilters(): array
    {
        return [
            new TwigFilter('country_emoji', $this->countryEmoji(...)),
        ];
    }

    public function countryEmoji(string $countryCode): string
    {
        return mb_chr(self::REGIONAL_OFFSET + mb_ord($countryCode[0], 'UTF-8'), 'UTF-8')
            .mb_chr(self::REGIONAL_OFFSET + mb_ord($countryCode[1], 'UTF-8'), 'UTF-8');
    }
}
