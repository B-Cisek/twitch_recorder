<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controller;

use App\Application\Channel\Command\Create\CreateChannelHandler;
use App\Application\Channel\Command\Delete\DeleteChannelCommand;
use App\Application\Channel\Command\Delete\DeleteChannelHandler;
use App\Application\Channel\Command\Update\UpdateChannelHandler;
use App\Application\Channel\Query\Result\Channel as ChannelQueryResult;
use App\Application\Channel\Query\GetChannel;
use App\Application\Channel\Query\GetChannels;
use App\Data\Entity\Channel;
use App\Infrastructure\Exception\NotFoundException;
use App\Infrastructure\Http\Request\CreateChannel;
use App\Infrastructure\Http\Request\UpdateChannel;
use DateTimeInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/channels')]
class ChannelController extends AbstractController
{
    public function __construct(
        private readonly GetChannel $getChannelQuery,
        private readonly GetChannels $getChannelsQuery,
        private readonly CreateChannelHandler $createChannelHandler,
        private readonly DeleteChannelHandler $deleteChannelHandler,
        private readonly UpdateChannelHandler $updateChannelHandler,
        private readonly PaginatorInterface $paginator,
    ) {
    }

    #[Route('', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateChannel $payload): JsonResponse
    {
        $this->createChannelHandler->handle($payload->toCommand());

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function getChannel(string $id): JsonResponse
    {
        $channel = $this->getChannelQuery->execute($id);

        if (null === $channel) {
            throw new NotFoundException('Channel not found');
        }

        return new JsonResponse($channel);
    }

    #[Route('', methods: ['GET'])]
    public function getChannels(Request $request): JsonResponse
    {
        $query = $this->getChannelsQuery->execute();

        $paginator = $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 5)
        );

        $data = [
            'data' => array_map(
                fn(Channel $channel) => new ChannelQueryResult(
                    $channel->getId()->toRfc4122(),
                    $channel->getName(),
                    $channel->getPlatform()->value,
                    $channel->isActive(),
                    $channel->getStartAt()?->format(DateTimeInterface::ATOM),
                    $channel->getEndAt()?->format(DateTimeInterface::ATOM),
                ),
                $paginator->getItems()
            ),
            'pagination' => [
                'current_page' => $paginator->getCurrentPageNumber(),
                'total_pages' => (int) (ceil($paginator->getTotalItemCount() / $paginator->getItemNumberPerPage())),
                'total_items' => $paginator->getTotalItemCount(),
            ]
        ];

        return new JsonResponse($data);
    }

    #[Route('/{id}', methods: ['PATCH'])]
    public function updateChannel(string $id, #[MapRequestPayload] UpdateChannel $payload): Response
    {
        $this->updateChannelHandler->handle($payload->toCommand($id));

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function deleteChannel(string $id): Response
    {
        $this->deleteChannelHandler->handle(new DeleteChannelCommand($id));

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}