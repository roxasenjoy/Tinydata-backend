<?php

namespace App\Repository;

use App\Entity\UserAccessVimeoContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserAccessVimeoContent|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserAccessVimeoContent|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserAccessVimeoContent[]    findAll()
 * @method UserAccessVimeoContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserAccessVimeoContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAccessVimeoContent::class);
    }
}
