<?php

namespace App\Repository;

use App\Entity\Profile;
use App\Service\GlobalFilterService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Profile|null find($id, $lockMode = null, $lockVersion = null)
 * @method Profile|null findOneBy(array $criteria, array $orderBy = null)
 * @method Profile[]    findAll()
 * @method Profile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfileRepository extends ServiceEntityRepository
{

    private $filterService;

    public function __construct(ManagerRegistry $registry, GlobalFilterService $filterService)
    {
        parent::__construct($registry, Profile::class);
        $this->filterService = $filterService;
    }

    public function getParcoursCompanies(){
        $companyFilter = $this->filterService->getAllCompaniesForUser();
        $qb =   $this->createQueryBuilder('profile')
                ->select('profile.jobTitle')
                ->leftJoin('profile.company', 'company');

        if($companyFilter){
            $qb->where('company.id IN (:company)')
            ->setParameter('company', $companyFilter);
        }

        return  $qb->getQuery()->getResult();
    }
}
