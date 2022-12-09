<?php

namespace App\Analytics\Plausible;

use App\Analytics\EventRequestInterface;

final readonly class EventRequest implements EventRequestInterface
{
    private function __construct(
        private string $userAgent,
        private string $xForwardedFor,
        private string $domain,
        private string $name,
        private string $url,
        private string $contentType = 'application/json',
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
