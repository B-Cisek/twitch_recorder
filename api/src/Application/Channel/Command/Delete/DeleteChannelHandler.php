<?php

declare(strict_types=1);

namespace App\Application\Channel\Command\Delete;

use App\Application\Channel\Provider\ChannelProvider;
use App\Application\Channel\Repository\Repository;
use Psr\Log\LoggerInterface;

readonly class DeleteChannelHandler
{
    public function __construct(
        private Repository $channelRepository,
        private ChannelProvider $channelProvider,
        private LoggerInterface $logger
    )
    {
    }

    public function handle(DeleteChannelCommand $command): void
    {
        $channel = $this->channelProvider->loadChannel($command->id);

        $this->channelRepository->remove($channel);

        $this->logger->info('Channel deleted', ['id' => $channel->getId(), 'name' => $channel->getName()]);
    }
}