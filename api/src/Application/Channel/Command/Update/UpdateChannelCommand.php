<?php

declare(strict_types=1);

namespace App\Application\Channel\Command\Update;

use App\Data\Enum\Platform;

readonly class UpdateChannelCommand
{
    public function __construct(
        public string $id,
        public ?string $name = null,
        public ?Platform $platform = null,
        public ?bool $isActive = null,
        public ?\DateTimeImmutable $startAt = null,
        public ?\DateTimeImmutable $endAt = null,
        public ?bool $isCurrentRecording = null,
    ) {
    }
}
