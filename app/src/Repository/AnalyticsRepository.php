<?php

namespace App\Repository;

use App\Entity\Analytics;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Analytics|null find($id, $lockMode = null, $lockVersion = null)
 * @method Analytics|null findOneBy(array $criteria, array $orderBy = null)
 * @method Analytics[]    findAll()
 * @method Analytics[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnalyticsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Analytics::class);
    }
}
