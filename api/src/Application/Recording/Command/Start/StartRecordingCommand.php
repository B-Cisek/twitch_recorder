<?php

declare(strict_types=1);

namespace App\Application\Recording\Command\Start;

use App\Data\Entity\Channel;

readonly class StartRecordingCommand
{
    public function __construct(public Channel $channel)
    {
    }
}
