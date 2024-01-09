<?php

namespace App\Repository;

use App\Entity\ImportHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImportHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImportHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImportHistory[]    findAll()
 * @method ImportHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImportHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImportHistory::class);
    }
}
