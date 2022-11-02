<?php

namespace App\Clock;

use StellaMaris\Clock\ClockInterface;

final class MockClock implements ClockInterface
{
    private ?\DateTimeImmutable $now;

    public function __construct(?\DateTimeImmutable $now = null)
    {
        if (null === $now) {
            $now = new DateTimeImmutable();
        }

        $this->now = $now;
    }

    public function setNow(\DateTimeImmutable $now): void
    {
        $this->now = $now;
    }

    public function now(): \DateTimeImmutable
    {
        return $this->now;
    }
}
