<?php

declare(strict_types=1);

namespace App\Infrastructure\Integration\Twitch;

use App\Infrastructure\Exception\UserNotFoundException;
use App\Infrastructure\Exception\UserNotLiveException;
use App\Infrastructure\Integration\Twitch\DTO\StreamInfo;
use App\Infrastructure\Integration\Twitch\DTO\UserInfo;

interface TwitchApiClientInterface
{
    /**
     * Get user information by username
     *
     * @param string $username The username to look up
     * @return UserInfo User data
     * @throws UserNotFoundException
     */
    public function getUserInfo(string $username): UserInfo;

    /**
     * Get the live status of a Twitch user by username.
     *
     * @param string $username The Twitch username to check
     * @return StreamInfo The live status data containing stream information
     * @throws UserNotLiveException
     */
    public function getStreamInfo(string $username): StreamInfo;
}