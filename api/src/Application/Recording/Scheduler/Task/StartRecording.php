<?php

declare(strict_types=1);

namespace App\Application\Recording\Scheduler\Task;

use App\Application\Channel\Repository\Repository;
use App\Application\Recording\Command\Start\StartRecordingCommand;
use App\Application\Recording\Command\Start\StartRecordingCommandHandler;
use Psr\Log\LoggerInterface;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCronTask('* * * * *')]
readonly class StartRecording
{
    public function __construct(
        private Repository $repository,
        private LoggerInterface $logger,
        private StartRecordingCommandHandler $startRecordingCommandHandler,
    )
    {
    }

    public function __invoke(): void
    {
        $channels = $this->repository->findAll();

        foreach ($channels as $channel) {
            if (!$channel->isActive() || $channel->isCurrentRecording()) {
                continue;
            }

            $this->startRecordingCommandHandler->handle(new StartRecordingCommand($channel));

            $this->logger->info('Start recording for channel', [
                'id' => $channel->getId()->toRfc4122(),
                'channel_name' => $channel->getName(),
            ]);
        }
    }
}