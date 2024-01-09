<?php

namespace App\Repository;

use App\Entity\ContentAttribute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ContentAttribute|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContentAttribute|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContentAttribute[]    findAll()
 * @method ContentAttribute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContentAttributeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContentAttribute::class);
    }
}
