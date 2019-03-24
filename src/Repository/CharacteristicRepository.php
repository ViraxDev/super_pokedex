<?php

namespace App\Repository;

use App\Entity\Characteristic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Characteristic|null find($id, $lockMode = null, $lockVersion = null)
 * @method Characteristic|null findOneBy(array $criteria, array $orderBy = null)
 * @method Characteristic[]    findAll()
 * @method Characteristic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CharacteristicRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Characteristic::class);
    }
}
