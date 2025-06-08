<?php

declare(strict_types=1);

namespace App\Application\Channel\Command\Create;

use App\Data\Enum\Platform;

readonly class CreateChannelCommand
{
    public function __construct(
        public string $name,
        public Platform $platform,
        public bool $isActive = false,
        public ?\DateTimeImmutable $startAt = null,
        public ?\DateTimeImmutable $endAt = null,
    ) {
    }
}