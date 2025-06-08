<?php

declare(strict_types=1);

namespace App\Application\Channel\Command\Update;

use App\Data\Enum\Platform;

final readonly class UpdateChannelCommand
{
    public function __construct(
        public string $id,
        public ?string $name,
        public ?Platform $platform,
        public ?bool $isActive,
        public ?\DateTimeImmutable $startAt,
        public ?\DateTimeImmutable $endAt
    ) {
    }
}