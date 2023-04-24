<?php

namespace App\Scheduler;

use Zenstruck\ScheduleBundle\Schedule;
use Zenstruck\ScheduleBundle\Schedule\ScheduleBuilder;

final class AppScheduleBuilder implements ScheduleBuilder
{
    public function buildSchedule(Schedule $schedule): void
    {
        $schedule
            ->timezone('UTC')
            ->environments('prod')
        ;

        $schedule->addCommand('app:send-jobsletter')
            ->description('Send the weekly jobs-letter to subscribers.')
            ->mondays()
            ->at('12:42')
        ;
    }
}
