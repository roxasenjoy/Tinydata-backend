<?php

namespace App\Repository;

use App\Entity\BoardingValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BoardingValue|null find($id, $lockMode = null, $lockVersion = null)
 * @method BoardingValue|null findOneBy(array $criteria, array $orderBy = null)
 * @method BoardingValue[]    findAll()
 * @method BoardingValue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoardingValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BoardingValue::class);
    }
}
