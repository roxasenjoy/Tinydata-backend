<?php

namespace App\Repository;

use App\Entity\UserLevel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserLevel|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserLevel|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserLevel[]    findAll()
 * @method UserLevel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserLevelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserLevel::class);
    }
}
