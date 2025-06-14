<?php

declare(strict_types=1);

namespace App\Application\Recording\Command\Stop;

readonly class StopRecordingCommand
{
    public function __construct(public string $recordingId)
    {
    }
}