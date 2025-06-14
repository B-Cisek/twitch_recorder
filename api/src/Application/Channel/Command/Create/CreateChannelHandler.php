<?php

declare(strict_types=1);

namespace App\Application\Channel\Command\Create;

use App\Application\Channel\Repository\Repository;
use App\Data\Entity\Channel;
use Psr\Log\LoggerInterface;

readonly class CreateChannelHandler
{
    public function __construct(
        private Repository $channelRepository,
        private LoggerInterface $logger
    ) {
    }

    public function handle(CreateChannelCommand $command): void
    {
        $channel = new Channel();
        $channel->setName($command->name);
        $channel->setPlatform($command->platform);
        $channel->setIsActive($command->isActive);
        $channel->setStartAt($command->startAt);
        $channel->setEndAt($command->endAt);

        $this->channelRepository->save($channel);

        $this->logger->info('Channel created', ['id' => $channel->getId(), 'name' => $channel->getName()]);
    }
}
