<?php

declare(strict_types=1);

namespace App\Application\Twitch\Service;

use App\Infrastructure\Exception\UserNotFoundException;
use App\Infrastructure\Integration\Twitch\DTO\StreamInfo;
use App\Infrastructure\Integration\Twitch\DTO\UserInfo;
use App\Infrastructure\Integration\Twitch\Exception\TwitchApiException;
use App\Infrastructure\Integration\Twitch\TwitchApiClientInterface;
use Psr\Log\LoggerInterface;

readonly class TwitchService
{
    public function __construct(
        private TwitchApiClientInterface $twitchApiClient,
        private LoggerInterface $logger
    )
    {
    }

    public function validateChannel(string $channelName): bool
    {
        try {
            $this->twitchApiClient->getUserInfo($channelName);
            return true;
        } catch (UserNotFoundException $e) {
            $this->logger->warning($e->getMessage(), ['given_channel' => $channelName]);
            return false;
        }
    }

    public function getStreamInfo(string $channelName): ?StreamInfo
    {
        try {
            return $this->twitchApiClient->getStreamInfo($channelName);
        } catch (TwitchApiException $e) {
            $this->logger->error('Failed to get stream info', [
                'channel' => $channelName,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function getUserInfo(string $channelName): ?UserInfo
    {
        try {
            return $this->twitchApiClient->getUserInfo($channelName);
        } catch (UserNotFoundException $e) {
            $this->logger->error('Failed to get user info', [
                'channel' => $channelName,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function isChannelLive(string $channelName): bool
    {
        try {
            $streamInfo = $this->twitchApiClient->getStreamInfo($channelName);
            return $streamInfo->type === 'live';
        } catch (TwitchApiException $e) {
            $this->logger->error('Failed to check if channel is live', [
                'channel' => $channelName,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}