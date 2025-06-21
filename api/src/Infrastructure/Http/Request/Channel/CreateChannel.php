<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Request\Channel;

use App\Application\Channel\Command\Create\CreateChannelCommand;
use App\Data\Entity\Channel;
use App\Data\Enum\Platform;
use App\Infrastructure\Http\Request\Validator\ValidTwitchNameConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity(
    fields: ['name'],
    message: 'A channel with this name already exists.',
    entityClass: Channel::class
)]
readonly class CreateChannel
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 255)]
        #[ValidTwitchNameConstraint]
        public string $name,
        #[Assert\NotBlank]
        #[Assert\Type(type: Platform::class)]
        public Platform $platform,
        #[Assert\Type(type: 'bool')]
        public bool $isActive = false,
        #[Assert\Type(\DateTimeImmutable::class)]
        #[Assert\GreaterThanOrEqual('today')]
        public ?\DateTimeImmutable $startAt = null,
        #[Assert\Type(\DateTimeImmutable::class)]
        #[Assert\GreaterThan(propertyPath: 'startAt')]
        public ?\DateTimeImmutable $endAt = null,
    ) {
    }

    public function toCommand(): CreateChannelCommand
    {
        return new CreateChannelCommand(
            $this->name,
            $this->platform,
            $this->isActive,
            $this->startAt,
            $this->endAt,
        );
    }
}
