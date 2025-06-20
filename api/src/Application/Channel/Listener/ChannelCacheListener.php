<?php

declare(strict_types=1);

namespace App\Application\Channel\Listener;

use App\Application\Recording\Scheduler\Task\StartRecording;
use App\Data\Entity\Channel;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\CacheInterface;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Channel::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: Channel::class)]
#[AsEntityListener(event: Events::postRemove, method: 'postRemove', entity: Channel::class)]
readonly class ChannelCacheListener
{
    private const string PERSIST_TYPE = 'persist';
    private const string UPDATE_TYPE = 'update';
    private const string DELETE_TYPE = 'delete';

    public function __construct(
        #[Autowire(service: 'cache.entity')]
        private CacheInterface $cache,
        private LoggerInterface $logger
    ) {
    }

    /** @param LifecycleEventArgs<EntityManagerInterface> $event */
    public function postPersist(Channel $channel, LifecycleEventArgs $event): void
    {
        $this->invalidateCache(self::PERSIST_TYPE);
    }

    /** @param LifecycleEventArgs<EntityManagerInterface> $event */
    public function postUpdate(Channel $channel, LifecycleEventArgs $event): void
    {
        $this->invalidateCache(self::UPDATE_TYPE);
    }

    /** @param LifecycleEventArgs<EntityManagerInterface> $event */
    public function postRemove(Channel $channel, LifecycleEventArgs $event): void
    {
        $this->invalidateCache(self::DELETE_TYPE);
    }

    private function invalidateCache(string $type): void
    {
        $this->cache->delete(StartRecording::CHANNELS_KEY);
        $this->logger->info('Channel cache invalidated', [
            'type' => $type,
        ]);
    }
}
