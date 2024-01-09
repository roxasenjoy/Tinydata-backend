<?php

namespace App\Repository;

use App\Entity\TcText;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TcText|null find($id, $lockMode = null, $lockVersion = null)
 * @method TcText|null findOneBy(array $criteria, array $orderBy = null)
 * @method TcText[]    findAll()
 * @method TcText[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TcTextRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TcText::class);
    }
}
