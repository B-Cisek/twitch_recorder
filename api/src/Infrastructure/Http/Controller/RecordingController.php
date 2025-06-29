<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controller;

use App\Application\Channel\Provider\ChannelProvider;
use App\Application\Recording\Command\Start\StartRecordingCommand;
use App\Application\Recording\Command\Start\StartRecordingCommandHandler;
use App\Application\Recording\Command\Stop\StopRecordingCommand;
use App\Application\Recording\Command\Stop\StopRecordingCommandHandler;
use App\Application\Recording\Command\Update\UpdateRecordingCommandHandler;
use App\Infrastructure\Http\Request\Recording\UpdateRecordingStatus;
use App\Infrastructure\Integration\Twitch\TwitchApiClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/recordings')]
class RecordingController extends AbstractController
{
    public function __construct(
        private readonly StopRecordingCommandHandler $stopRecordingCommandHandler,
        private readonly UpdateRecordingCommandHandler $updateRecordingStatusCommandHandler,
        private readonly StartRecordingCommandHandler $startRecordingCommandHandler,
    ) {
    }

    #[Route('/{id}/stop', methods: ['POST'])]
    public function stop(string $id): JsonResponse
    {
        $this->stopRecordingCommandHandler->handle(new StopRecordingCommand($id));

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}/update', methods: ['PATCH'])]
    public function updateStatus(
        string $id,
        #[MapRequestPayload] UpdateRecordingStatus $request
    ): JsonResponse {
        $this->updateRecordingStatusCommandHandler->handle($request->toCommand($id));

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }

    // TEST ENDPOINT REMOVE
    #[Route('/start', methods: ['get'])]
    public function start(ChannelProvider $provider): JsonResponse
    {
        $this->startRecordingCommandHandler->handle(new StartRecordingCommand('01974cab-2784-7e50-83d8-37fe1f2a3dcc'));

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }

    // test to remove
    #[Route('/test', methods: ['get'])]
    public function test(TwitchApiClientInterface $twitchApiClient, ChannelProvider $provider): JsonResponse
    {
        $res = $twitchApiClient->getUserInfo('delordione');
        return new JsonResponse(status: Response::HTTP_OK);
    }
}
