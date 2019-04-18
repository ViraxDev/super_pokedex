<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Pokemon;
use Doctrine\Common\Persistence\ManagerRegistry;
use Pagerfanta\Pagerfanta;

class PokemonRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pokemon::class);
    }

    /**
     * @param string $term
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @param string $type
     * @return Pagerfanta
     */
    public function search(string $term, string $order = 'asc', int $limit = 60, int $offset = 0, string $type = ""): Pagerfanta
    {
        $qb = $this
            ->createQueryBuilder('p')
            ->select('p')
            ->orderBy('p.name', $order);

        if ($term) {
            $qb
                ->where('p.name LIKE ?1')
                ->setParameter(1, '%' . $term . '%');
        }

        if ($type) {
            $qb
                ->join('p.types', 'types')
                ->andWhere('types.label LIKE ?2')
                ->setParameter(2, '%' . $type . '%');
        }

        return $this->paginate($qb, $limit, $offset);
    }
}
