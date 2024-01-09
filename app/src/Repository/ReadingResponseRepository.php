<?php

namespace App\Repository;

use App\Entity\ReadingResponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReadingResponse|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReadingResponse|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReadingResponse[]    findAll()
 * @method ReadingResponse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReadingResponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReadingResponse::class);
    }
}
