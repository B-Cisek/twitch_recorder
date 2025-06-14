<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controller;

use App\Application\Recording\Command\Stop\StopRecordingCommand;
use App\Application\Recording\Command\Stop\StopRecordingCommandHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/recordings')]
class RecordingController extends AbstractController
{
    public function __construct(
        private readonly StopRecordingCommandHandler $stopRecordingCommandHandler
    ) {
    }

    #[Route('/{id}/stop', methods: ['POST'])]
    public function create(string $id): JsonResponse
    {
        $this->stopRecordingCommandHandler->handle(new StopRecordingCommand($id));

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }
}