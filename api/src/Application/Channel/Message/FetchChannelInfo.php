<?php

declare(strict_types=1);

namespace App\Application\Channel\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage(transport: 'async')]
readonly class FetchChannelInfo
{
    public function __construct(public string $username)
    {
    }
}