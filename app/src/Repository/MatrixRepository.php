<?php

namespace App\Repository;

use App\Entity\Matrix;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Service\GlobalFilterService;

/**
 * @method Matrix|null find($id, $lockMode = null, $lockVersion = null)
 * @method Matrix|null findOneBy(array $criteria, array $orderBy = null)
 * @method Matrix[]    findAll()
 * @method Matrix[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MatrixRepository extends ServiceEntityRepository
{

    const MATRIX_DEPTH = 1;
    const DOMAIN_DEPTH = 2;
    const SKILL_DEPTH = 3;
    const THEMES_DEPTH = 4;
    const ACQUISITION_DEPTH = 7;

    private $filterService;

    public function __construct(ManagerRegistry $registry, GlobalFilterService $filterService)
    {
        parent::__construct($registry, Matrix::class);
        $this->filterService = $filterService;
    }

    public function getMatrixSpecificData($args){
        $companyFilter = $this->filterService->getAllCompaniesForUser();

        $qb = $this->createQueryBuilder('matrix')
            ->select("matrix.id as formationId, matrix.name as name")
            ->distinct(true)
            ->leftJoin('matrix.companies', 'company')
            ->andWhere('company.deletedAt IS NULL')
            ->orderBy('matrix.name');

        if($args['organisationsFilter']){
            $qb->andWhere('company.id IN (:companies)')
                        ->setParameter(':companies', $args['organisationsFilter']);
        }

        if($args['matrixFilter']){
            $qb->andWhere('matrix.id IN (:matrixFilter)')
                        ->setParameter(':matrixFilter', $args['matrixFilter']);
        }

        if($companyFilter){            
            $qb->andWhere("company.id IN (:companyList)")
            ->setParameter('companyList', $companyFilter);
        }

        return $qb->getQuery()->getResult();
    }

    public function getMatrixEntity($matrixId){
        $matrix = $this->createQueryBuilder('matrix')
                    ->where('matrix.id = (:matrixId)')
                    ->setParameter('matrixId',$matrixId)
                    ->getQuery()
                    ->getResult();
        
        return $matrix;
    }
    public function findMatrixTinydata($researchBar){

        $filterCompany = $this->filterService->getAllCompaniesForUser();

        $qb = $this->createQueryBuilder('matrix')
        ->join('matrix.companies', 'company');

        $qb->andWhere("matrix.name like :researchBar")
        ->setParameter(':researchBar', '%'.$researchBar.'%');

        if($filterCompany){
            $qb->andWhere('company.id IN (:companyList)')
            ->setParameter(':companyList', $filterCompany);
        }

        $qb->orderBy('matrix.name');

        return $qb->getQuery()->getResult();         
    }

    public function totalMatrixes($companies){
        $companyFilter = $this->filterService->getAllCompaniesForUser();

        $qb = $this->createQueryBuilder('matrix')
            ->select("COUNT(distinct matrix.id)")
            ->leftJoin('matrix.companies', 'company')
            ->orderBy('matrix.name');

            if($companies){
                $qb->andWhere('company.id IN (:companies)')
                            ->setParameter('companies', $companies);
            }

        if($companyFilter){           
            $qb->andWhere("company.id IN (:companyList)")
            ->setParameter('companyList', $companyFilter);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Ajouter une variable pour définir la page à laquelle on se trouve Numéro de page * 10
     */
    public function getAllMatrixes($companies, $matrixFilter, $userId){

        $companyFilter = $this->filterService->getAllCompaniesForUser();

        $qb = $this->createQueryBuilder('matrix')
            ->select("matrix.id, matrix.name")
            ->distinct(true)
            ->leftJoin('matrix.companies', 'company')
            ->andWhere('company.deletedAt IS NULL')
            ->orderBy('matrix.name');

        if($companies){
            $qb->andWhere('company.id IN (:companies)')
                        ->setParameter(':companies', $companies);
        }

        if($matrixFilter){
            $qb->andWhere('matrix.id IN (:matrixFilter)')
                        ->setParameter(':matrixFilter', $matrixFilter);
        }

        if($companyFilter){            
            $qb->andWhere("company.id IN (:companyList)")
            ->setParameter('companyList', $companyFilter);
        }
        
        return $qb->getQuery()->getResult();
    }




    public function findMatrixByAcquisition($acquisId){
        $matrixQuery = $this->createQueryBuilder('matrix')
            ->join('matrix.domains', 'domain')
            ->join('domain.skills', 'skill')
            ->join('skill.skillLevels', "skillLevel")
            ->join('skillLevel.acquisitions', 'acquisition')
            ->where('acquisition.id = :acquisition')
            ->setParameter('acquisition', $acquisId);
        $matrix = $matrixQuery->getQuery()->getOneOrNullResult();
        return $matrix;
    }

    public function findMatrixByTheme($themeId){
        $matrixQuery = $this->createQueryBuilder('matrix')
            ->join('matrix.domains', 'domain')
            ->join('domain.skills', 'skill')
            ->join('skill.themes', 'theme')
            ->where('theme.id = :theme')
            ->setParameter('theme', $themeId);
        $matrix = $matrixQuery->getQuery()->getOneOrNullResult();
        return $matrix;
    }

    public function findMatrixBySkill($skillId){
        $matrixQuery = $this->createQueryBuilder('matrix')
            ->join('matrix.domains', 'domain')
            ->join('domain.skills', 'skill')
            ->where('skill.id = :skill')
            ->setParameter('skill', $skillId);
        $matrix = $matrixQuery->getQuery()->getOneOrNullResult();
        return $matrix;
    }

    public function findMatrixByDomain($domainId){
        $matrixQuery = $this->createQueryBuilder('matrix')
            ->join('matrix.domains', 'domain')
            ->where('domain.id = :domain')
            ->setParameter('domain', $domainId);
        $matrix = $matrixQuery->getQuery()->getOneOrNullResult();
        return $matrix;
    }

    public function findMatrixesForUser($organisationsFilter = null, $matrixFilter = null, $actualGranularity = null){
        $companyFilter = $this->filterService->getAllCompaniesForUser();

        $matrixQuery = $this->createQueryBuilder('matrix')
            ->leftJoin('matrix.companies', 'company')
            ->orderBy('matrix.name');

        if($companyFilter){           
            $matrixQuery->andWhere("company.id IN (:companyList)")
            ->setParameter('companyList', $companyFilter);
        }

        if($matrixFilter){
            $matrixQuery->andWhere("matrix.id IN (:matrixFilter)")
                        ->setParameter("matrixFilter", $matrixFilter);
        }

        if($organisationsFilter){           
            $matrixQuery->andWhere("company.id IN (:companyFilter)")
            ->setParameter('companyFilter', $organisationsFilter);
        }

        if($actualGranularity){
            $matrixQuery->andWhere("matrix.id IN (:actualMatrix)")
                        ->setParameter("actualMatrix", $actualGranularity);
        }

        $matrixes = $matrixQuery->getQuery()->getResult();

        return $matrixes;
    }

    public function getNumberOfUserByMatrix($args){
        $userCompanies = $this->filterService->getAllCompaniesForUser();

        $matrixQuery = $this->createQueryBuilder('matrix')
        ->select('matrix.id, count(matrix.id) as count')
        ->join('matrix.companies', 'company')
        ->join('company.users', 'userClient')
        ->join('userClient.user', 'user')
        ->groupBy('matrix.id');

        if($userCompanies){           
            $matrixQuery->andWhere("company.id IN (:companyList)")
            ->setParameter('companyList', $userCompanies);
        }

        if($args['idUser']){
            $matrixQuery->andWhere("user.id IN (:userId)")
                        ->setParameter("userId", $args['idUser']);
        }

        if($args['organisationsFilter']){           
            $matrixQuery->andWhere("company.id IN (:companyFilter)")
            ->setParameter('companyFilter', $args['organisationsFilter']);
        }

        return $matrixQuery->getQuery()->getResult();
    }
}
