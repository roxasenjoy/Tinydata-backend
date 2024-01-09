<?php

namespace App\Repository;

use App\Entity\UserConnections;
use App\Service\DateService;
use App\Service\GlobalFilterService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserConnections|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserConnections|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserConnections[]    findAll()
 * @method UserConnections[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserConnectionsRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserConnections::class);
    }
}
