<?php

namespace App\Repository;

use App\Entity\QuickReply;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method QuickReply|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuickReply|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuickReply[]    findAll()
 * @method QuickReply[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuickReplyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuickReply::class);
    }
}
