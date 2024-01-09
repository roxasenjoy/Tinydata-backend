<?php

namespace App\Repository;

use App\Entity\BoardingAttributes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BoardingAttributes|null find($id, $lockMode = null, $lockVersion = null)
 * @method BoardingAttributes|null findOneBy(array $criteria, array $orderBy = null)
 * @method BoardingAttributes[]    findAll()
 * @method BoardingAttributes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoardingAttributesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BoardingAttributes::class);
    }
}
