<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Request\Validator;

use App\Application\Twitch\Service\TwitchService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidTwitchNameValidator extends ConstraintValidator
{
    public function __construct(private readonly TwitchService $twitchService)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidTwitchNameConstraint) {
            throw new \InvalidArgumentException();
        }

        if (!$this->twitchService->validateChannel($value)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
