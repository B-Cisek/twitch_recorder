<?php

declare(strict_types=1);

namespace App\Application\Channel\Query\Result;

final readonly class Channel
{
    public function __construct(
        public string $id,
        public string $name,
        public string $platform,
        public bool $isActive,
        public ?string $startAt = null,
        public ?string $endAt = null,
    )
    {
    }
}