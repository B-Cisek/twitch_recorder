<?php

declare(strict_types=1);

namespace App\Application\Channel\Event;

use App\Data\Entity\Channel;
use Symfony\Contracts\EventDispatcher\Event;

class ChannelCreated extends Event
{
    public const string NAME = 'channel.created';

    public function __construct(public readonly Channel $channel)
    {
    }
}
