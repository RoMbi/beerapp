<?php

namespace App\Repository;

use App\Entity\Beer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class BeerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Beer::class);
    }

    /**
     * @param int $limit
     * @param int $offset
     * @param array $filters
     *
     * @return array
     */
    public function findAllFilteredPaginated(int $limit, int $offset, array $filters = []): array
    {
        $qb = $this->createQueryBuilder('b')
            ->select('b', 'br')
            ->leftJoin('b.brewer', 'br');

        $this->applyFilters($filters, $qb);

        $qb->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * @param array $filters
     * @param QueryBuilder $qb
     */
    private function applyFilters(array $filters, QueryBuilder $qb): void
    {
        if (isset($filters['brewerIds'])) {
            $qb->andWhere('br.id IN (:brewerIds)')
                ->setParameter('brewerIds', $filters['brewerIds']);
        }
        if (isset($filters['name'])) {
            $qb->andWhere('b.name LIKE :name')
                ->setParameter('name', '%' . $filters['name'] . '%');
        }
        if (isset($filters['priceFrom'])) {
            $qb->andWhere('b.price >= :priceFrom')
                ->setParameter('priceFrom', $filters['priceFrom']);
        }
        if (isset($filters['priceTo'])) {
            $qb->andWhere('b.price <= :priceTo')
                ->setParameter('priceTo', $filters['priceTo']);
        }
        if (isset($filters['country'])) {
            // @todo by country code
            $qb->andWhere('b.country LIKE :country')
                ->setParameter('country', '%' . $filters['country'] . '%');
        }
        if (isset($filters['type'])) {
            $qb->andWhere('b.type LIKE :type')
                ->setParameter('type', '%' . $filters . '%');
        }
    }
}
