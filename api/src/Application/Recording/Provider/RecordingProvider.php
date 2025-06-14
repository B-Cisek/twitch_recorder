<?php

declare(strict_types=1);

namespace App\Application\Recording\Provider;

use App\Application\Recording\Exception\RecordingNotFoundException;
use App\Application\Recording\Repository\QueryRepository;
use App\Data\Entity\Recording;

readonly class RecordingProvider
{
    public function __construct(private QueryRepository $queryRepository)
    {
    }

    public function loadRecording(string $id): Recording
    {
        $recording = $this->queryRepository->find($id);

        if (null === $recording) {
            throw new RecordingNotFoundException();
        }

        return $recording;
    }
}
