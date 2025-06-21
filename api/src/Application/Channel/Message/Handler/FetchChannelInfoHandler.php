<?php

declare(strict_types=1);

namespace App\Application\Channel\Message\Handler;

use App\Application\Channel\Message\FetchChannelInfo;
use App\Application\Channel\Provider\ChannelProvider;
use App\Application\Channel\Repository\Repository;
use App\Application\Twitch\Service\TwitchService;
use App\Data\Entity\ChannelInfo;
use App\Data\Enum\BroadcasterType;
use App\Data\Enum\ChannelType;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class FetchChannelInfoHandler
{
    public function __construct(
        private TwitchService $twitchService,
        private ChannelProvider $channelProvider,
        private Repository $repository,
        private LoggerInterface $logger
    )
    {
    }

    public function __invoke(FetchChannelInfo $message): void
    {
        $channel = $this->channelProvider->loadChannelByName($message->username);
        $channelInfo = $this->twitchService->getUserInfo($message->username);

        $info = new ChannelInfo()
            ->setChannelId($channelInfo->id)
            ->setLogin($channelInfo->login)
            ->setDisplayName($channelInfo->displayName)
            ->setChannelType(ChannelType::from($channelInfo->type))
            ->setBroadcasterType(BroadcasterType::from($channelInfo->broadcasterType))
            ->setDescription($channelInfo->description)
            ->setProfileImageUrl($channelInfo->profileImageUrl)
            ->setOfflineImageUrl($channelInfo->offlineImageUrl)
            ->setCreatedAt($channelInfo->createdAt);

        $channel->setChannelInfo($info);
        $this->repository->save($channel);

        $this->logger->info('Channel info was fetched and stored', [
            'channel_id' => $channel->getId()->toRfc4122()
        ]);
    }
}