<?php

declare(strict_types=1);

namespace App\Application\Channel\Command\Update;

use App\Application\Channel\Provider\ChannelProvider;
use App\Application\Channel\Repository\Repository;
use Psr\Log\LoggerInterface;

readonly class UpdateChannelHandler
{
    public function __construct(
        private Repository $channelRepository,
        private ChannelProvider $channelProvider,
        private LoggerInterface $logger
    ) {
    }

    public function handle(UpdateChannelCommand $command): void
    {
        $channel = $this->channelProvider->loadChannel($command->id);

        if ($command->name !== null) {
            $channel->setName($command->name);
        }
        if ($command->platform !== null) {
            $channel->setPlatform($command->platform);
        }
        if ($command->isActive !== null) {
            $channel->setIsActive($command->isActive);
        }
        if ($command->startAt !== null) {
            $channel->setStartAt($command->startAt);
        }
        if ($command->endAt !== null) {
            $channel->setEndAt($command->endAt);
        }

        if ($command->isCurrentRecording !== null) {
            $channel->setCurrentRecording($command->isCurrentRecording);
        }

        $this->channelRepository->save($channel);

        $this->logger->info('Channel updated', ['id' => $command->id, 'name' => $command->name]);
    }
}
