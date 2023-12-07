<?php

namespace App;

use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

final class Scheduler implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    {
        return new Schedule();
    }
}
