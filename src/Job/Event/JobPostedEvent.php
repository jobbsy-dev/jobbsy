<?php

namespace App\Job\Event;

use App\Entity\Job;
use Symfony\Contracts\EventDispatcher\Event;

final class JobPostedEvent extends Event
{
    public function __construct(
        public readonly Job $job,
        public readonly string $jobUrl,
    ) {
    }
}
