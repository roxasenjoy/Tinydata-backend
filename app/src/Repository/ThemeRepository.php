<?php

namespace App\Repository;

use App\Entity\Theme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Service\GlobalFilterService;

/**
 * @method Theme|null find($id, $lockMode = null, $lockVersion = null)
 * @method Theme|null findOneBy(array $criteria, array $orderBy = null)
 * @method Theme[]    findAll()
 * @method Theme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ThemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, GlobalFilterService $filterService)
    {
        parent::__construct($registry, Theme::class);
        $this->filterService = $filterService;
    }

    public function findThemesTinydata($researchBar){
        $filterCompany = $this->filterService->getAllCompaniesForUser();

        $qb = $this->createQueryBuilder('theme')
        ->join('theme.skill', 'skill')
        ->join('skill.domain', 'domain')
        ->join('domain.matrix', 'matrix')
        ->join('matrix.companies', 'company');

        $qb->where("theme.title like :researchBar")
        ->setParameter(':researchBar', '%'.$researchBar.'%');

        if($filterCompany){
            $qb->andWhere('company.id IN (:companyList)')
            ->setParameter(':companyList', $filterCompany);
        }

        $qb->orderBy('theme.title');

        return $qb->getQuery()->getResult();
    }

    public function getAllThemes(){
        $companyFilter = $this->filterService->getAllCompaniesForUser();
                    
        $themeQuery = $this->createQueryBuilder('theme')
            ->select("theme.id, theme.title, skill.id as skillId, domain.id as domainId, matrix.id as matrixId")
            ->distinct(true)
            ->leftJoin('theme.skill', 'skill')
            ->leftJoin('skill.domain', 'domain')
            ->leftJoin('domain.matrix', 'matrix')
            ->leftJoin('matrix.companies', 'company')
            ->orderBy('theme.title');
        if($companyFilter){
            $themeQuery->where("company.id IN (:companyList)")
            ->setParameter('companyList', $companyFilter);
        }
        $themes = $themeQuery->getQuery()->getResult();
        return $themes;
    }
}
