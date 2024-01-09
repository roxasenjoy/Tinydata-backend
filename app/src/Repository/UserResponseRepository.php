<?php

namespace App\Repository;

use App\Entity\UserResponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserResponse|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserResponse|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserResponse[]    findAll()
 * @method UserResponse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserResponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserResponse::class);
    }
}
