<?php

namespace App\Repository;

use App\Entity\RefreshContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RefreshContent|null find($id, $lockMode = null, $lockVersion = null)
 * @method RefreshContent|null findOneBy(array $criteria, array $orderBy = null)
 * @method RefreshContent[]    findAll()
 * @method RefreshContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RefreshContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RefreshContent::class);
    }
}
