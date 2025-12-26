<?php

namespace App\CommunityEvent;

enum AttendanceMode: string
{
    case MIXED = 'mixed';

    case OFFLINE = 'offline';

    case ONLINE = 'online';
}
