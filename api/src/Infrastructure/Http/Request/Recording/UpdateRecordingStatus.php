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
    private const string DATE_FORMAT = 'Y-m-d\TH:i:s.v\Z';

    #[Assert\Choice(callback: [RecordingStatus::class, 'values'])]
    public ?string $status = null;

    #[Assert\DateTime(format: self::DATE_FORMAT)]
    public ?string $startedAt = null;

    #[Assert\DateTime(format: self::DATE_FORMAT)]
    public ?string $endedAt = null;

    #[Assert\Length(max: 255)]
    public ?string $url = null;

    public function toCommand(string $id): UpdateRecordingCommand
    {
        return new UpdateRecordingCommand(
            id: $id,
            status: $this->status ? RecordingStatus::from($this->status) : null,
            startedAt: $this->startedAt ? DateTimeImmutable::createFromFormat(self::DATE_FORMAT, $this->startedAt) : null,
            endedAt: $this->endedAt ? DateTimeImmutable::createFromFormat(self::DATE_FORMAT, $this->endedAt) : null,
            url: $this->url
        );
    }
}
