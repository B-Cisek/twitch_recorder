<?php

declare(strict_types=1);

namespace App\Data\Enum;

enum ChannelType: string
{
    case ADMIN = 'admin';
    case GLOBAL_MOD = 'global_mod';
    case STAFF = 'staff';
    CASE NORMAL_USER = '';
}