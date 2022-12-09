<?php

namespace App\Provider\Arbeitsagentur;

final readonly class OrganizationLogo
{
    public function __construct(
        public string $content,
        public ?string $contentType
    ) {
    }
}
