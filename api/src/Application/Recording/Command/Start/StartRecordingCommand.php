<?php

declare(strict_types=1);

namespace App\Application\Recording\Command\Start;

readonly class StartRecordingCommand
{
    public function __construct(public string $channelId)
    {
    }
}
