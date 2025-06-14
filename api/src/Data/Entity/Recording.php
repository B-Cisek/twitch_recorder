<?php

declare(strict_types=1);

namespace App\Data\Entity;

use App\Application\Recording\Repository\Repository;
use App\Data\Enum\RecordingStatus;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: Repository::class)]
class Recording extends BaseEntity
{
    #[ORM\ManyToOne(inversedBy: 'recordings')]
    #[ORM\JoinColumn(nullable: false)]
    private Channel $channel;

    #[ORM\Column(type: Types::STRING, enumType: RecordingStatus::class)]
    private RecordingStatus $status;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $startedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $endedAt = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $filePath = null;

    public function getChannel(): Channel
    {
        return $this->channel;
    }

    public function setChannel(Channel $channel): Recording
    {
        $this->channel = $channel;

        return $this;
    }

    public function getStatus(): RecordingStatus
    {
        return $this->status;
    }

    public function setStatus(RecordingStatus $status): Recording
    {
        $this->status = $status;
        return $this;
    }

    public function getStartedAt(): ?DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function setStartedAt(?DateTimeImmutable $startedAt): Recording
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    public function getEndedAt(): ?DateTimeImmutable
    {
        return $this->endedAt;
    }

    public function setEndedAt(?DateTimeImmutable $endedAt): Recording
    {
        $this->endedAt = $endedAt;
        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): Recording
    {
        $this->filePath = $filePath;
        return $this;
    }
}
