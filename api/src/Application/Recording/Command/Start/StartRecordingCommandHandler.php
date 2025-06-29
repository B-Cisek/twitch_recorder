<?php

declare(strict_types=1);

namespace App\Application\Recording\Command\Start;

use App\Application\Channel\Provider\ChannelProvider;
use App\Application\Recording\Message\StartRecording;
use App\Application\Recording\Repository\Repository;
use App\Data\Entity\Recording;
use App\Data\Enum\RecordingStatus;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class StartRecordingCommandHandler
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private Repository $repository,
        private LoggerInterface $logger,
        private ChannelProvider $provider
    ) {
    }

    public function handle(StartRecordingCommand $command): void
    {
        $channel = $this->provider->loadChannel($command->channelId);
        $recording = new Recording();
        $recording->setChannel($channel);
        $recording->setStatus(RecordingStatus::PENDING);

        $this->repository->save($recording);

        $this->logger->info('New Recording created', [
            'recording_id' => $recording->getId()->toRfc4122(),
            'channel_name' => $channel->getName(),
        ]);

        $this->messageBus->dispatch(new StartRecording(
            $recording->getId()->toRfc4122(),
            $recording->getChannel()->getName(),
            $recording->getChannel()->getPlatform()->value,
        ));

        $this->logger->info('Start recording message dispatched', [
            'recording_id' => $recording->getId()->toRfc4122(),
        ]);
    }
}
