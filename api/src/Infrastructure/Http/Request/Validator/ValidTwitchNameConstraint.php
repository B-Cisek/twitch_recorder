<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Request\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ValidTwitchNameConstraint extends Constraint
{
    public string $message = 'Provided name does not exist in twitch.';

    public function getTargets(): string|array
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return ValidTwitchNameValidator::class;
    }
}