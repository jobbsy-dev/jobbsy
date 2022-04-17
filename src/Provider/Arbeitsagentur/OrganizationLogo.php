<?php

namespace App\Provider\Arbeitsagentur;

final class OrganizationLogo
{
    public function __construct(
        public readonly string $content,
        public readonly ?string $contentType
    ) {
    }
}
