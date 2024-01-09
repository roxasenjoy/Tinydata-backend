<?php

namespace App\Repository;

use App\Entity\UserDevice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserDevice|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserDevice|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserDevice[]    findAll()
 * @method UserDevice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserDeviceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserDevice::class);
    }
}
