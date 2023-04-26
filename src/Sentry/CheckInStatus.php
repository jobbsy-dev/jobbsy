<?php

namespace App\Sentry;

enum CheckInStatus: string
{
    case inProgress = 'in_progress';
    case ok = 'ok';
    case error = 'error';
}
