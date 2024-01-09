<?php

namespace App\Repository;

use App\Entity\UserLevelGoals;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserLevelGoals|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserLevelGoals|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserLevelGoals[]    findAll()
 * @method UserLevelGoals[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserLevelGoalsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserLevelGoals::class);
    }
}
