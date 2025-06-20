<?php

declare(strict_types=1);

namespace App\Infrastructure\Integration\Twitch\Enum;

enum StreamType: string
{
    case ALL = 'all';
    case LIVE = 'live';
}
