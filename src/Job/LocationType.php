<?php

namespace App\Job;

enum LocationType: string
{
    case REMOTE = 'remote';

    case ONSITE = 'onsite';

    case HYBRID = 'hybrid';
}
