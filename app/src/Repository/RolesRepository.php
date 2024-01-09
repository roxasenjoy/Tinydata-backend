<?php

namespace App\Repository;

use App\Service\GlobalFilterService;
use App\Service\UserService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class RolesRepository extends ServiceEntityRepository
{
    private $filterService;
    private $userService;

    public function __construct(
        ManagerRegistry $registry, 
        GlobalFilterService $filterService, 
        UserService $userService
        )
    {
        parent::__construct($registry, Roles::class);
        $this->filterService = $filterService;
        $this->userService = $userService;
    }
}
