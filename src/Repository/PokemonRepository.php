<?php

namespace App\Repository;

use App\Entity\Pokemon;
use Doctrine\Common\Persistence\ManagerRegistry;

class PokemonRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pokemon::class);
    }

    public function search($term, $order = 'asc', $limit = 60, $offset = 0, $type = "")
    {
        {
            $qb = $this
                ->createQueryBuilder('p')
                ->select('p')
                ->orderBy('p.name', $order)
            ;

            if ($term) {
                $qb
                    ->where('p.name LIKE ?1')
                    ->setParameter(1, '%'.$term.'%')
                ;
            }

            if ($type) {
                $qb
                    ->join('p.types', 'types')
                    ->andWhere('types.label LIKE ?2')
                    ->setParameter(2, '%'.$type.'%')
                ;
            }

            return $this->paginate($qb, $limit, $offset);
        }
    }
}
