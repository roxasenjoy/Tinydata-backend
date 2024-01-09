<?php

namespace App\Repository;

use App\Entity\Domain;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Service\GlobalFilterService;

/**
 * @method Domain|null find($id, $lockMode = null, $lockVersion = null)
 * @method Domain|null findOneBy(array $criteria, array $orderBy = null)
 * @method Domain[]    findAll()
 * @method Domain[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DomainRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, GlobalFilterService $filterService)
    {
        parent::__construct($registry, Domain::class);
        $this->filterService = $filterService;
    }

    public function findDomainsTinydata($researchBar){

		$filterCompany = $this->filterService->getAllCompaniesForUser();

        $qb = $this->createQueryBuilder('domain')
        ->join('domain.matrix', 'matrix')
        ->join('matrix.companies', 'company');

        $qb->where("domain.title like :researchBar")
        ->setParameter(':researchBar', '%'.$researchBar.'%');

        if($filterCompany){
            $qb->andWhere('company.id IN (:companyList)')
            ->setParameter(':companyList', $filterCompany);
        }

        $qb->groupBy('domain.id');
        $qb->orderBy('domain.title');

        return $qb->getQuery()->getResult();
    }


	public function getAllDomains(){
        $companyFilter = $this->filterService->getAllCompaniesForUser();
                    
        $qb = $this->createQueryBuilder('domain')
            ->select("domain.id, domain.title, matrix.id as matrixId")
			->distinct(true)
            ->leftJoin('domain.matrix', 'matrix')
            ->leftJoin('matrix.companies', 'company')
            ->orderBy('domain.title');
		if($companyFilter){
            $qb->where("company.id IN (:companyList)")
			->setParameter('companyList', $companyFilter);
		}

        return $qb->getQuery()->getResult();
    }

}
