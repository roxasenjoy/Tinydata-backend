<?php

namespace App\Repository;

use App\Entity\Query;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Query|null find($id, $lockMode = null, $lockVersion = null)
 * @method Query|null findOneBy(array $criteria, array $orderBy = null)
 * @method Query[]    findAll()
 * @method Query[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QueryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Query::class);
    }
}
