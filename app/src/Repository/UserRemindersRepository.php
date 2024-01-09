<?php

namespace App\Repository;

use App\Entity\UserReminders;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserReminders|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserReminders|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserReminders[]    findAll()
 * @method UserReminders[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRemindersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserReminders::class);
    }
}
