<?php

declare(strict_types=1);

namespace App\Application\Channel\Command\Delete;

readonly class DeleteChannelCommand
{
    public function __construct(public string $id)
    {
    }
}