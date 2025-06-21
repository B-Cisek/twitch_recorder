<?php

declare(strict_types=1);

namespace App\Data\Entity;

use App\Data\Enum\BroadcasterType;
use App\Data\Enum\ChannelType;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ChannelInfo extends BaseEntity
{
    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    private string $channelId;

    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    private string $login;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $displayName;

    #[ORM\Column(type: Types::STRING, enumType: ChannelType::class)]
    private ChannelType $channelType;

    #[ORM\Column(type: Types::STRING, enumType: BroadcasterType::class)]
    private BroadcasterType $broadcasterType;

    #[ORM\Column(type: Types::TEXT)]
    private string $description;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $profileImageUrl;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $offlineImageUrl;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    public function getChannelId(): string
    {
        return $this->channelId;
    }

    public function setChannelId(string $channelId): ChannelInfo
    {
        $this->channelId = $channelId;
        return $this;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): ChannelInfo
    {
        $this->login = $login;
        return $this;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): ChannelInfo
    {
        $this->displayName = $displayName;
        return $this;
    }

    public function getChannelType(): ChannelType
    {
        return $this->channelType;
    }

    public function setChannelType(ChannelType $channelType): ChannelInfo
    {
        $this->channelType = $channelType;
        return $this;
    }

    public function getBroadcasterType(): BroadcasterType
    {
        return $this->broadcasterType;
    }

    public function setBroadcasterType(BroadcasterType $broadcasterType): ChannelInfo
    {
        $this->broadcasterType = $broadcasterType;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): ChannelInfo
    {
        $this->description = $description;
        return $this;
    }

    public function getProfileImageUrl(): string
    {
        return $this->profileImageUrl;
    }

    public function setProfileImageUrl(string $profileImageUrl): ChannelInfo
    {
        $this->profileImageUrl = $profileImageUrl;
        return $this;
    }

    public function getOfflineImageUrl(): string
    {
        return $this->offlineImageUrl;
    }

    public function setOfflineImageUrl(string $offlineImageUrl): ChannelInfo
    {
        $this->offlineImageUrl = $offlineImageUrl;
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): ChannelInfo
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
