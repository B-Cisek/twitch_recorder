<?php

declare(strict_types=1);

namespace App\Infrastructure\Integration\Twitch\DTO;

use DateTimeImmutable;

readonly class UserInfo
{
    public function __construct(
        public string $id,
        public string $login,
        public string $displayName,
        public string $type,
        public string $broadcasterType,
        public string $description,
        public string $profileImageUrl,
        public string $offlineImageUrl,
        public string $viewCount,
        public DateTimeImmutable $createdAt,
    )
    {
    }
}