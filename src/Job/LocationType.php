<?php

namespace App\Job;

enum LocationType: string
{
    case REMOTE = 'remote';
    case ON_SITE = 'on_site';
    case HYBRID = 'hybrid';
}
