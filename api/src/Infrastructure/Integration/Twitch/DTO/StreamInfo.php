<?php

declare(strict_types=1);

namespace App\Infrastructure\Integration\Twitch\DTO;

use App\Infrastructure\Integration\Twitch\Enum\StreamType;
use DateTimeImmutable;

readonly class StreamInfo
{
    public function __construct(
        public string $id,
        public string $userId,
        public string $userLogin,
        public string $userName,
        public string $gameId,
        public string $gameName,
        public StreamType $type,
        public string $title,
        public int $viewerCount,
        public DateTimeImmutable $startedAt,
        public string $language,
        public string $thumbnailUrl,
        public bool $isMature,
    )
    {
    }
}