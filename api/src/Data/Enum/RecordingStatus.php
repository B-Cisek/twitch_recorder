<?php

declare(strict_types=1);

namespace App\Data\Enum;

enum RecordingStatus: string
{
    case PENDING = 'pending';
    case RECORDING = 'recording';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
