<?php

declare(strict_types=1);

namespace App\Application\Channel\Query;

use App\Data\Entity\Channel;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;

final readonly class GetChannels
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function execute(): Query
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb
            ->select('c')
            ->from(Channel::class, 'c');

        return $qb->getQuery();
    }
}