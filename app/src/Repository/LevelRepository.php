<?php

namespace App\Repository;

use App\Entity\Level;
use App\Entity\Skill;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Level|null find($id, $lockMode = null, $lockVersion = null)
 * @method Level|null findOneBy(array $criteria, array $orderBy = null)
 * @method Level[]    findAll()
 * @method Level[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LevelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Level::class);
    }

    public function getUserLevel(Skill $skill, $user)
    {
        $query = $this->createQueryBuilder('l')
            ->innerJoin('l.userLevels', 'userLevel')
            ->innerJoin('userLevel.userClient', 'user')
            ->innerJoin('userLevel.skill', 'skill')
            ->where('user.id = :userId')
            ->andWhere('skill.id = :skillId')
            ->setParameter('userId', $user)
            ->setParameter('skillId', $skill->getId())
            ->orderBy('userLevel.date', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        return $query->getOneOrNullResult();
    }
}
