<?php

namespace App\News\Aggregator;

final class XmlHelper
{
    public static function getNodeValue(
        \DOMXPath $xpath,
        string $query,
        \DOMNode $element = null,
        int $index = 0
    ): mixed {
        $nodeList = $xpath->query($query, $element);

        if (false === $nodeList) {
            return false;
        }

        if ($nodeList->length <= 0) {
            return null;
        }

        if ($index > $nodeList->length) {
            return null;
        }

        return self::sanitizeValue($nodeList->item($index)?->nodeValue);
    }

    private static function sanitizeValue(mixed $value): mixed
    {
        if ('true' === $value) {
            return true;
        }

        if ('false' === $value) {
            return false;
        }

        if (empty($value)) {
            return null;
        }

        return $value;
    }
}
