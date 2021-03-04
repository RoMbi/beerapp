<?php

namespace App\Repository;

use App\Dto\BrewerDto;
use App\Entity\Brewer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BrewerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Brewer::class);
    }

    /**
     * @return array
     */
    public function findAllWithBeerCount(): array
    {
        $qb = $this->createQueryBuilder('br')
            ->select(sprintf(
                'NEW %s(br.id, br.name, COUNT(be))',
                BrewerDto::class
            ))
            ->leftJoin('br.beers', 'be')
            ->groupBy('br.id')
            ->getQuery();

        return $qb->getResult();
    }
}
