<?php

declare(strict_types=1);

namespace App\Application\Channel\Query;

use App\Application\Channel\Query\Result\Channel;
use App\Data\Entity\Channel as ChannelEntity;
use Doctrine\ORM\EntityManagerInterface;

final readonly class GetChannel
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function execute(string $id): ?Channel
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb
            ->select('c')
            ->from(ChannelEntity::class, 'c')
            ->where('c.id = :id')
            ->setParameter('id', $id);

        /** @var ChannelEntity $result */
        $result = $qb->getQuery()->getOneOrNullResult();

        if (null === $result) {
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