<?php

declare(strict_types=1);

namespace App\Application\Recording\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage(transport: 'async')]
readonly class StartRecording
{
    public function __construct(
        public string $recordingId,
        public string $channel,
        public string $platform,
    ) {
    }
}
