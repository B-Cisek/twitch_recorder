<?php

declare(strict_types=1);

namespace App\Application\Recording\Provider;

use App\Application\Channel\Exception\ChannelNotFoundException;
use App\Application\Recording\Repository\QueryRepository;
use App\Data\Entity\Recording;

readonly class RecordingProvider
{
    public function __construct(private QueryRepository $queryRepository)
    {
    }

    public function loadChannel(string $id): Recording
    {
        $channel = $this->queryRepository->find($id);

        if (null === $channel) {
            throw new ChannelNotFoundException();
        }

        return $channel;
    }
}
