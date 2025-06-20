<?php

declare(strict_types=1);

namespace App\Infrastructure\Exception;

use App\Infrastructure\Integration\Twitch\Exception\TwitchApiException;

class UserNotLiveException extends TwitchApiException
{
    public function __construct(string $username)
    {
        parent::__construct('User is not currently streaming: ' . $username);
    }
}
