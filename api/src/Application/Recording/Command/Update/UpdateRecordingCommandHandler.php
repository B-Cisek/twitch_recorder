<?php

declare(strict_types=1);

namespace App\Application\Recording\Command\Update;

use App\Application\Recording\Provider\RecordingProvider;
use App\Application\Recording\Repository\Repository;
use Psr\Log\LoggerInterface;

readonly class UpdateRecordingCommandHandler
{
    public function __construct(
        private RecordingProvider $provider,
        private LoggerInterface $logger,
        private Repository $repository
    ) {
    }

    public function handle(UpdateRecordingCommand $command): void
    {
        $recording = $this->provider->loadRecording($command->id);

        if ($command->status !== null) {
            $recording->setStatus($command->status);
        }

        if ($command->startedAt !== null) {
            $recording->setStartedAt($command->startedAt);
        }

        if ($command->endedAt !== null) {
            $recording->setEndedAt($command->endedAt);
        }

        $this->repository->save($recording);

        $this->logger->info('Recording status updated', [
            'recording_id' => $recording->getId()->toRfc4122(),
            'status' => $command->status?->value,
            'started_at' => $command->startedAt?->format('Y-m-d H:i:s'),
            'ended_at' => $command->endedAt?->format('Y-m-d H:i:s'),
        ]);
    }
}
