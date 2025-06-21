<?php

declare(strict_types=1);

namespace App\Application\Channel\Listener;

use App\Application\Channel\Event\ChannelCreated;
use App\Application\Channel\Message\FetchChannelInfo;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsEventListener(event: ChannelCreated::class, method: 'fetchChannelInfo')]
readonly class ChannelCreatedListener
{
    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function fetchChannelInfo(ChannelCreated $event): void
    {
        $this->messageBus->dispatch(new FetchChannelInfo($event->channel->getName()));
    }
}
