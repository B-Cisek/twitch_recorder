<?php

declare(strict_types=1);

namespace App\Application\Recording\Command\Update;

use App\Data\Enum\RecordingStatus;
use DateTimeImmutable;

readonly class UpdateRecordingCommand
{
    public function __construct(
        public string $id,
        public ?RecordingStatus $status = null,
        public ?DateTimeImmutable $startedAt = null,
        public ?DateTimeImmutable $endedAt = null,
        public ?string $filePath = null
    ) {
    }
}