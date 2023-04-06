<?php

namespace App\News\Aggregator;

final class XmlHelper
{
    public static function getNodeValue(\DOMXPath $xpath, $query, \DOMNode $element = null, $index = 0): mixed
    {
        $nodeList = $xpath->query($query, $element);
        if ($nodeList->length <= 0) {
            return null;
        }

        if ($index > $nodeList->length) {
            return null;
        }

        return self::sanitizeValue($nodeList->item($index)->nodeValue);
    }

    private static function sanitizeValue($value): mixed
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
