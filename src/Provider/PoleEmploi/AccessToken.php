<?php

namespace App\Provider\PoleEmploi;

use Webmozart\Assert\Assert;

class AccessToken
{
    private \DateTimeImmutable $createdAt;

    private function __construct(
        private readonly string $token,
        private readonly int $expiresIn,
    ) {
        Assert::notEmpty($token);
        Assert::greaterThan($this->expiresIn, 0);

        $this->createdAt = new \DateTimeImmutable();
    }

    public static function create(string $token, int $expiresIn): self
    {
        return new self($token, $expiresIn);
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function hasExpired(): bool
    {
        return new \DateTimeImmutable() > $this->createdAt->add(new \DateInterval(sprintf('PT%sS', $this->expiresIn)));
    }
}
