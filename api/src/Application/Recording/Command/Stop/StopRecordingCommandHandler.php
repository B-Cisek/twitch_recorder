<?php

declare(strict_types=1);

namespace App\Application\Recording\Command\Stop;

use App\Application\Recording\Message\StopRecording;
use App\Application\Recording\Provider\RecordingProvider;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class StopRecordingCommandHandler
{
    public function __construct(
        private RecordingProvider $provider,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function handle(StopRecordingCommand $command): void
    {
        $recording = $this->provider->loadRecording($command->recordingId);

        $this->messageBus->dispatch(new StopRecording($recording->getId()->toRfc4122()));
    }
}
