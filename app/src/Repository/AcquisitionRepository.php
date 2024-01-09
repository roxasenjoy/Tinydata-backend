<?php

namespace App\Repository;

use App\Entity\Acquisition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Service\GlobalFilterService;

/**
 * @method Acquisition|null find($id, $lockMode = null, $lockVersion = null)
 * @method Acquisition|null findOneBy(array $criteria, array $orderBy = null)
 * @method Acquisition[]    findAll()
 * @method Acquisition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AcquisitionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, GlobalFilterService $filterService)
    {
        parent::__construct($registry, Acquisition::class);
        $this->filterService = $filterService;
    }


    public function findAcquisitionTinydata($researchBar){
        $filterCompany = $this->filterService->getAllCompaniesForUser();

        $qb = $this->createQueryBuilder('acquis')
		->join('acquis.theme', 'theme')
		->join('theme.skill','skill')
        ->join('skill.domain', 'domain')
        ->join('domain.matrix', 'matrix')
        ->join('matrix.companies', 'company');

        $qb->where("acquis.title like :researchBar")
        ->setParameter(':researchBar', '%'.$researchBar.'%');

        if($filterCompany){
            $qb->andWhere('company.id IN (:companyList)')
            ->setParameter(':companyList', $filterCompany);
        }

        $qb->groupBy('acquis.id');
        $qb->orderBy('acquis.title');

        return $qb->getQuery()->getResult();
    }

    //Graphique - PROGRESSION GENERALE
    public function getCountAcquisFromSkill($skill, $level)
    {
        $qb = $this->createQueryBuilder('acquis')
                ->join('acquis.theme', 'theme')
                ->join('theme.skill', 'skill')
                ->join('skill.domain', 'domain')
                ->join('domain.matrix', 'matrix')

                ->andWhere("theme.skill = :skill")
                ->setParameter("skill", $skill);

        return $qb->getQuery()->getResult();
    }
}
