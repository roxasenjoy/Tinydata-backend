<?php

namespace App\Repository;

use App\Entity\QueryReading;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method QueryReading|null find($id, $lockMode = null, $lockVersion = null)
 * @method QueryReading|null findOneBy(array $criteria, array $orderBy = null)
 * @method QueryReading[]    findAll()
 * @method QueryReading[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QueryReadingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QueryReading::class);
    }
}
