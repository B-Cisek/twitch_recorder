<?php

declare(strict_types=1);

namespace App\Application\Channel\Exception;

use App\Infrastructure\Exception\NotFoundException;

class ChannelNotFoundException extends NotFoundException
{
    /** @var string */
    protected $message = 'Channel not found';
}
