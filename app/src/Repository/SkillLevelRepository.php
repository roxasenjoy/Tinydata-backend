<?php

namespace App\Repository;

use App\Entity\SkillLevel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SkillLevel|null find($id, $lockMode = null, $lockVersion = null)
 * @method SkillLevel|null findOneBy(array $criteria, array $orderBy = null)
 * @method SkillLevel[]    findAll()
 * @method SkillLevel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SkillLevelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SkillLevel::class);
    }
}
