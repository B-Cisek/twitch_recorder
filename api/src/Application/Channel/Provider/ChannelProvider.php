<?php

declare(strict_types=1);

namespace App\Application\Channel\Provider;

use App\Application\Channel\Exception\ChannelNotFoundException;
use App\Application\Channel\Repository\QueryRepository;
use App\Data\Entity\Channel;

readonly class ChannelProvider
{
    public function __construct(private QueryRepository $queryRepository)
    {
    }

    public function loadChannel(string $id): Channel
    {
        $channel = $this->queryRepository->find($id);

        if (null === $channel) {
            throw new ChannelNotFoundException();
        }

        return $channel;
    }
}