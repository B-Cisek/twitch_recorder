<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Request\Recording;

use App\Application\Recording\Command\Update\UpdateRecordingCommand;
use App\Data\Enum\RecordingStatus;
use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateRecordingStatus
{
    #[Assert\Choice(callback: [RecordingStatus::class, 'cases'])]
    public ?string $status = null;

    #[Assert\DateTime(format: DateTimeInterface::ATOM)]
    public ?string $startedAt = null;

    #[Assert\DateTime(format: DateTimeInterface::ATOM)]
    public ?string $endedAt = null;

    #[Assert\Length(max: 255)]
    public ?string $url = null;

    public function toCommand(string $id): UpdateRecordingCommand
    {
        return new UpdateRecordingCommand(
            id: $id,
            status: $this->status ? RecordingStatus::from($this->status) : null,
            startedAt: $this->startedAt ? DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $this->startedAt) : null,
            endedAt: $this->endedAt ? DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $this->endedAt) : null,
            url: $this->url
        );
    }
}
