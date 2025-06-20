<?php

declare(strict_types=1);

namespace App\Application\Recording\Scheduler\Task;

use App\Application\Channel\Repository\Repository;
use App\Application\Recording\Command\Start\StartRecordingCommand;
use App\Application\Recording\Command\Start\StartRecordingCommandHandler;
use App\Application\Twitch\Service\TwitchService;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Scheduler\Attribute\AsCronTask;
use Symfony\Contracts\Cache\CacheInterface;

#[AsCronTask('* * * * *')]
readonly class StartRecording
{
    public const string CHANNELS_KEY = 'channels';

    public function __construct(
        private Repository $repository,
        private LoggerInterface $logger,
        private StartRecordingCommandHandler $startRecordingCommandHandler,
        private TwitchService $twitchService,
        #[Autowire(service: 'cache.entity')]
        private CacheInterface $cache,
    ) {
    }

    public function __invoke(): void
    {
        $channels = $this->cache->get(self::CHANNELS_KEY, function () {
            $this->logger->info('Cached channels');
            return $this->repository->findAll();
        });

        foreach ($channels as $channel) {
            if (!$channel->isActive() || $channel->isCurrentRecording() || !$this->isLive($channel->getName())) {
                continue;
            }

            $this->startRecordingCommandHandler->handle(new StartRecordingCommand($channel));

            $this->logger->info('Start recording for channel', [
                'id' => $channel->getId()->toRfc4122(),
                'channel_name' => $channel->getName(),
            ]);
        }
    }

    private function isLive(string $channelName): bool
    {
        return $this->twitchService->isChannelLive($channelName);
    }
}
