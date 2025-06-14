<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Request\Channel;

use App\Application\Channel\Command\Update\UpdateChannelCommand;
use App\Data\Entity\Channel;
use App\Data\Enum\Platform;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity(
    fields: ['name'],
    message: 'A channel with this name already exists.',
    entityClass: Channel::class
)]
readonly class UpdateChannel
{
    public function __construct(
        #[Assert\Length(min: 3, max: 255)]
        public ?string $name = null,
        #[Assert\Type(type: Platform::class)]
        public ?Platform $platform = null,
        #[Assert\Type(type: 'bool')]
        public ?bool $isActive = null,
        #[Assert\Type(\DateTimeImmutable::class)]
        #[Assert\GreaterThanOrEqual('today')]
        public ?\DateTimeImmutable $startAt = null,
        #[Assert\Type(\DateTimeImmutable::class)]
        #[Assert\GreaterThan(propertyPath: 'startAt')]
        public ?\DateTimeImmutable $endAt = null,
    ) {
    }

    public function toCommand(string $id): UpdateChannelCommand
    {
        return new UpdateChannelCommand(
            $id,
            $this->name,
            $this->platform,
            $this->isActive,
            $this->startAt,
            $this->endAt,
        );
    }
}
