<?php

declare(strict_types=1);

namespace App\Application\Recording\Exception;

use App\Infrastructure\Exception\NotFoundException;

class RecordingNotFoundException extends NotFoundException
{
    /** @var string */
    protected $message = 'Recording not found';
}