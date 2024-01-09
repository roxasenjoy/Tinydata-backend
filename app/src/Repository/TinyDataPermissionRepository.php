<?php

namespace App\Repository;

use App\Entity\TinyDataPermission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class TinyDataPermissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TinyDataPermission::class);
    }

    public function getPermissionsFromRole(Int $roleId)
    {
        $qb = $this->createQueryBuilder('p')
        ->select('p.id, p.name, p.description')
        ->join('p.roles', 'permissionRole')
        ->where('permissionRole.id = :roleId')
        ->setParameter('roleId', $roleId);

        return $qb->getQuery()->getResult();
    }
    
}
