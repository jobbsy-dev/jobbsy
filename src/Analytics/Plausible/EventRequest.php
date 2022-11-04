<?php

namespace App\Analytics\Plausible;

use App\Analytics\EventRequestInterface;

final class EventRequest implements EventRequestInterface
{
    private function __construct(
        private readonly string $userAgent,
        private readonly string $xForwardedFor,
        private readonly string $domain,
        private readonly string $name,
        private readonly string $url,
        private readonly string $contentType = 'application/json',
    ) {
    }

    public static function create(array $data): self
    {
        return new self(
            $data['User-Agent'],
            $data['X-Forwarded-For'],
            $data['domain'],
            $data['name'],
            $data['url'],
            $data['Content-Type'] ?? 'application/json',
        );
    }

    public function headers(): array
    {
        return [
            'User-Agent' => $this->userAgent,
            'X-Forwarded-For ' => $this->xForwardedFor,
            'Content-Type' => $this->contentType,
        ];
    }

    public function body(): array
    {
        return [
            'domain' => $this->domain,
            'name' => $this->name,
            'url' => $this->url,
        ];
    }
}
