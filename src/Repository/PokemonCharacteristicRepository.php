<?php

namespace App\Repository;

use App\Entity\PokemonCharacteristic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PokemonCharacteristic|null find($id, $lockMode = null, $lockVersion = null)
 * @method PokemonCharacteristic|null findOneBy(array $criteria, array $orderBy = null)
 * @method PokemonCharacteristic[]    findAll()
 * @method PokemonCharacteristic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PokemonCharacteristicRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PokemonCharacteristic::class);
    }
}
