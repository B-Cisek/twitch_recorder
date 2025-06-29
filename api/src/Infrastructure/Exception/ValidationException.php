<?php

declare(strict_types=1);

namespace App\Infrastructure\Exception;

use RuntimeException;

class ValidationException extends RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
