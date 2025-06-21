<?php

declare(strict_types=1);

namespace App\Data\Enum;

enum BroadcasterType: string
{
    case AFFILIATE = 'affiliate';
    case PARTNER = 'partner';
    case NORMAL_BROADCASTER = '';
}