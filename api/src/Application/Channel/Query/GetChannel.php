<?php

declare(strict_types=1);

namespace App\Application\Channel\Query;

use App\Application\Channel\Query\Result\Channel;
use App\Data\Entity\Channel as ChannelEntity;
use Doctrine\ORM\EntityManagerInterface;

readonly class GetChannel
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function execute(string $id): ?Channel
    {
        /** @var ChannelEntity|null $result */
        $result = $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(ChannelEntity::class, 'c')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$result instanceof ChannelEntity) {
            return null;
        }

        return new Channel(
            $result->getId()->toRfc4122(),
            $result->getName(),
            $result->getPlatform()->value,
            $result->isActive(),
            $result->getStartAt()?->format(\DateTimeInterface::ATOM),
            $result->getEndAt()?->format(\DateTimeInterface::ATOM),
        );
    }
}
