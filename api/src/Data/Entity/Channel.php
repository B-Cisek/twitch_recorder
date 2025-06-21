<?php

declare(strict_types=1);

namespace App\Data\Entity;

use App\Application\Channel\Repository\Repository;
use App\Data\Enum\Platform;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: Repository::class)]
class Channel extends BaseEntity
{
    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    private string $name;

    #[ORM\Column(type: Types::STRING, enumType: Platform::class)]
    private Platform $platform;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $isActive = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $startAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $endAt = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $isCurrentRecording = false;

    /** @var Collection<int, Recording> */
    #[ORM\OneToMany(targetEntity: Recording::class, mappedBy: 'channel')]
    private Collection $recordings;

    #[ORM\OneToOne(targetEntity: ChannelInfo::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?ChannelInfo $channelInfo = null;

    public function __construct()
    {
        parent::__construct();
        $this->recordings = new ArrayCollection();
    }

    public function isCurrentRecording(): bool
    {
        return $this->isCurrentRecording;
    }

    public function setCurrentRecording(bool $isCurrentRecording): Channel
    {
        $this->isCurrentRecording = $isCurrentRecording;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Channel
    {
        $this->name = $name;
        return $this;
    }

    public function getPlatform(): Platform
    {
        return $this->platform;
    }

    public function setPlatform(Platform $platform): Channel
    {
        $this->platform = $platform;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): Channel
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getStartAt(): ?\DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(?\DateTimeImmutable $startAt): Channel
    {
        $this->startAt = $startAt;
        return $this;
    }

    public function getEndAt(): ?\DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(?\DateTimeImmutable $endAt): Channel
    {
        $this->endAt = $endAt;
        return $this;
    }

    /** @return Collection<int, Recording> */
    public function getRecordings(): Collection
    {
        return $this->recordings;
    }

    public function addRecording(Recording $recording): static
    {
        if (!$this->recordings->contains($recording)) {
            $this->recordings->add($recording);
            $recording->setChannel($this);
        }

        return $this;
    }

    public function getChannelInfo(): ?ChannelInfo
    {
        return $this->channelInfo;
    }

    public function setChannelInfo(ChannelInfo $channelInfo): Channel
    {
        $this->channelInfo = $channelInfo;
        return $this;
    }
}
