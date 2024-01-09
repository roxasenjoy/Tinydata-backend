<?php

namespace App\Repository;

use App\Entity\Content;
use App\Service\GlobalFilterService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Content|null find($id, $lockMode = null, $lockVersion = null)
 * @method Content|null findOneBy(array $criteria, array $orderBy = null)
 * @method Content[]    findAll()
 * @method Content[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContentRepository extends ServiceEntityRepository
{

    private $filterService;

    public function __construct(ManagerRegistry $registry, GlobalFilterService $filterService)
    {
        parent::__construct($registry, Content::class);
        $this->filterService = $filterService;
    }

    public function getContentStatusForAllUsers($args, $specificGraphData){

        $userCompanies = $this->filterService->getAllCompaniesForUser();

        $qb = $this ->createQueryBuilder('content')
        ->select('user.firstName', 'user.lastName', 'user.email', 'company.name as companyName', 'parent1.name as parent1Name', 'matrix.name as matrixName', 'COUNT(content) as contentCount')

        ->join('content.userContents', 'userContents')
        ->join('userContents.userClient', 'userClient')
        ->join('userClient.company', 'company')
        ->leftJoin('company.parent1', 'parent1')
        ->join('userClient.user', 'user')
        ->join("content.valueAttribute", "valueAttribute")
        ->join("content.acquisition", "acquis")
        ->join("acquis.theme", "theme")
        ->join("theme.skill", "skill")
        ->join("skill.domain", "domain")
        ->join("domain.matrix", "matrix")
        
        /* Liste des conditions pour qu'un contenu soit validé */
        ->where('userContents.isAlreadyValidated = 0')
        ->andWhere('content.active = 1')
        ->andWhere('content.deletedAt IS NULL')
        ->andWhere('valueAttribute.attribute = 2')
        ->andWhere('userContents.date > :beginDate')
        ->andWhere('userContents.date < :endDate')
        ->setParameter('beginDate', $args['beginDate'])
        ->setParameter('endDate', $args['endDate'])
        ;

        if($specificGraphData['isValidated'] === "true"){
            $qb->andWhere('userContents.status = 1');
        }else{
            $qb->andWhere('userContents.status = 0');
        }

        if($args['matrixFilter']){
            $qb->andWhere('matrix.id IN (:matrixId)')
            ->setParameter('matrixId', $args['matrixFilter']);
        }
        
        if($args['domainFilter']){
            $qb->andWhere('domain.id IN (:domainId)')
            ->setParameter('domainId', $args['domainFilter']);
        }
        
        if($args['skillFilter']){
            $qb->andWhere('skill.id IN (:skillId)')
            ->setParameter('skillId', $args['skillFilter']);
        }

        if($args['themeFilter']){
            $qb->andWhere('theme.id IN (:themeId)')
            ->setParameter('themeId', $args['themeFilter']);
        }
        
        if($args['organisationsFilter']){
            $qb->andWhere('userClient.company IN (:organisationsFilter)')
            ->setParameter('organisationsFilter', $args['organisationsFilter']);
        }
        
        if($args['idUser']){
            $qb->andWhere('user.id = :id')
            ->setParameter('id', $args['idUser']);
        }

        if($userCompanies){
            $qb->andWhere('userClient.company IN (:company_id)')
            ->setParameter('company_id', $userCompanies);
        }

        if ($specificGraphData['isExtended'] === 'true' && $specificGraphData['_pageIndex'] !== null) {
            /* Nous avons besoin de toutes les informations des users actifs */
                $nbBeginReturn = ($specificGraphData['_pageIndex'] - 1) * 10;
                $qb ->setFirstResult($nbBeginReturn)
                    ->setMaxResults(10);
        }

        $qb->groupBy('user.id, matrix.id');
            
        return $qb->getQuery()->getResult();
        
        
    }

    public function getTotalContent($args, $specificGraphData){

        $userCompanies = $this->filterService->getAllCompaniesForUser();

        $qb = $this ->createQueryBuilder('content')
        ->select('count(content.id)')
        ->join('content.userContents', 'userContents')
        ->join('userContents.userClient', 'userClient')
        ->join('userClient.company', 'company')
        ->join('userClient.user', 'user')

        ->join('content.acquisition', 'acquis')
        ->innerJoin("acquis.theme", "theme")
        ->join('acquis.skillLevel', 'skillLevel')
        ->join('skillLevel.skill', 'skill')
        ->join('skill.domain', 'domain')
        ->join('domain.matrix', 'matrix')
        
        /* Liste des conditions pour qu'un contenu soit validé */
        ->where('userContents.isAlreadyValidated = 0')
        ->andWhere('content.active = 1')
        ->andWhere('content.deletedAt IS NULL')
        ->andWhere('userContents.date > :beginDate')
        ->andWhere('userContents.date < :endDate')
        ->setParameter('beginDate', $args['beginDate'])
        ->setParameter('endDate', $args['endDate'])
        ;

        if($specificGraphData['isValidated'] === "true"){
            $qb->andWhere('userContents.status = 1');
        }else{
            $qb->andWhere('userContents.status = 0');
        }

        if($args['matrixFilter']){
            
            $qb->andWhere('matrix.id IN (:matrixId)')
            ->setParameter('matrixId', $args['matrixFilter']);
        }
        
        if($args['domainFilter']){
            $qb->andWhere('domain.id IN (:domainId)')
            ->setParameter('domainId', $args['domainFilter']);
        }
        
        if($args['skillFilter']){
            $qb->andWhere('skill.id IN (:skillId)')
            ->setParameter('skillId', $args['skillFilter']);
        }

        if($args['themeFilter']){
            $qb->andWhere('theme.id IN (:themeId)')
            ->setParameter('themeId', $args['themeFilter']);
        }
        
        if($args['organisationsFilter']){
            $qb->andWhere('userClient.company IN (:organisationsFilter)')
            ->setParameter('organisationsFilter', $args['organisationsFilter']);
        }
        
        if($args['idUser']){
            $qb->andWhere('user.id = :id')
            ->setParameter('id', $args['idUser']);
        } else {
            if($userCompanies){
                $qb->andWhere('userClient.company IN (:company_id)')
                ->setParameter('company_id', $userCompanies);

            }
        }
            
        return (int)$qb->getQuery()->getSingleScalarResult();
        
        
    }

    public function getFilterContentNumberByGranularity($granularityLevel, $args){
        
        if(!array_key_exists('organisationsFilter', $args) || !$args['organisationsFilter']){
            $args['organisationsFilter'] = $this->filterService->getAllCompaniesForUser();
        }
        
        $qb = $this->createQueryBuilder('content')
        ->join("content.acquisition", "acquisition")
        ->join("acquisition.theme", "theme")
        ->join("theme.skill", "skill")
        ->join("skill.domain", "domain")
        ->join("domain.matrix", "matrix")
        
        ;
        //->join("matrix.companies", "company");

        //Choix du group by a réaliser en fonction de ce que l'on veut récup
        switch($granularityLevel){
            case $this->filterService::DEPTH_MATRIX:
                $qb->select("count(content.id) as total, matrix.id as id, matrix.name")
                ->groupBy("matrix.id");
                break;
            case $this->filterService::DEPTH_DOMAIN:
                $qb->select("count(content.id) as total, matrix.id as id, domain.id, domain.title")
                ->groupBy("domain.id");
                break;
            case $this->filterService::DEPTH_SKILL:
                $qb->select("count(content.id) as total, matrix.id as id, skill.id, skill.title")
                ->groupBy("skill.id");
                break;
            case $this->filterService::DEPTH_THEME:
                $qb->select("count(content.id) as total, matrix.id as id, skill.id, theme.id, theme.title")
                ->groupBy("theme.id");
                break;
        }

        $this->filterService->setFormationsFilterInRepository($args, $qb);

        return $qb->getQuery()->getResult(); 
    }

    public function getContentNumberByGranularityForGeneralProgression($granularityLevel, $args){
        
        if(!array_key_exists('organisationsFilter', $args) || !$args['organisationsFilter']){
            $args['organisationsFilter'] = $this->filterService->getAllCompaniesForUser();
        }
        
        $qb = $this->createQueryBuilder('content')
        ->join("content.acquisition", "acquisition")
        ->join("acquisition.theme", "theme")
        ->join("theme.skill", "skill")
        ->join("skill.domain", "domain")
        ->join("domain.matrix", "matrix")
        
        ;
        
        if(!$args['filterFormationSelected']){
            $args['filterFormationSelected'] = '';
        }

        if($args['filterFormationSelected']){
            $qb->andWhere("matrix.id = :filterFormationSelected")
            ->setParameter('filterFormationSelected', $args['filterFormationSelected']);
        }

        //Choix du group by a réaliser en fonction de ce que l'on veut récup
        switch($granularityLevel){
            case $this->filterService::DEPTH_MATRIX:
                $qb->select("count(content.id) as total, matrix.id as id, matrix.name")
                ->groupBy("matrix.id");
                break;
            case $this->filterService::DEPTH_DOMAIN:
                $qb->select("count(content.id) as total, matrix.id as id, domain.id, domain.title")
                ->groupBy("domain.id");
                break;
            case $this->filterService::DEPTH_SKILL:
                $qb->select("count(content.id) as total, matrix.id as id, skill.id, skill.title")
                ->groupBy("skill.id");
                break;
            case $this->filterService::DEPTH_THEME:
                $qb->select("count(content.id) as total, matrix.id as id, skill.id, theme.id, theme.title")
                ->groupBy("theme.id");
                break;
        }

        $this->filterService->setFormationsFilterInRepository($args, $qb);

        return $qb->getQuery()->getResult(); 
    }

    public function getContentNumberByGranularity($args, $specificGraphData = []){

        if($args['organisationsFilter']){
            $args['organisationsFilter'] = $this->filterService->getAllCompaniesForUser();
        }
        
        $qb = $this->createQueryBuilder('content')
        ->join("content.acquisition", "acquisition")
        ->join("acquisition.theme", "theme")
        ->join("theme.skill", "skill")
        ->join("skill.domain", "domain")
        ->join("domain.matrix", "matrix");
        
        if(!array_key_exists('filterFormationSelected', $args)){
            $args['filterFormationSelected'] = '';
        }

        if($args['filterFormationSelected']){
            $qb->andWhere("matrix.id = :filterFormationSelected")
            ->setParameter('filterFormationSelected', $args['filterFormationSelected']);
        }

        //Choix du group by a réaliser en fonction de ce que l'on veut récup
        switch($specificGraphData['nameDepth']){
            case 'matrix':
                $qb->select("count(content.id) as total, matrix.id as id, matrix.name")
                ->groupBy("matrix.id");
                break;
            case 'domain':
                $qb->select("count(content.id) as total, matrix.id as id, domain.id, domain.title")
                ->groupBy("domain.id");
                break;
            case 'skill':
                $qb->select("count(content.id) as total, matrix.id as id, skill.id, skill.title")
                ->groupBy("skill.id");
                break;
            case 'theme':
                $qb->select("count(content.id) as total, matrix.id as id, skill.id, theme.id, theme.title")
                ->groupBy("theme.id");
                break;
        }

        $this->filterService->setFormationsFilterInRepository($args, $qb);

        return $qb->getQuery()->getResult(); 
    }


    /**
     * Utile pour : GRAPHIQUE PERFORMANCE
     * 
     * Compte le nombre d'acquis présent dans les granularités
     * 
     * @param granularityType: matrix / domain / skill / theme
     */
    public function countAcquisByGranularity($filter, $granularityType){

        $qb = $this ->createQueryBuilder('content');

                    // Granularités
                    $qb ->innerJoin('content.acquisition', 'acquis')
                        ->innerJoin("acquis.theme", "theme")
                        ->innerJoin('theme.skill', 'skill')
                        ->innerJoin("skill.domain", "domain")
                        ->innerJoin('domain.matrix', 'matrix');
                   
                    //Choix du group by a réaliser en fonction de ce que l'on veut récup
                    switch($granularityType){
                        case 'matrix':
                            $qb ->select("count(acquis.id) as acquisTotal, matrix.id as matrixId, matrix.name as title")
                                ->groupBy("matrix.id");
                            break;
                        case 'domain':
                            $qb ->select("count(acquis.id) as acquisTotal, matrix.id as matrixId, domain.id as domainId, domain.title")
                                ->groupBy("domain.id");
                            break;
                        case 'skill':
                            $qb ->select("count(acquis.id) as acquisTotal, matrix.id as matrixId, skill.id as skillId, skill.title")
                                ->groupBy("skill.id");
                            break;
                        case 'theme':
                            $qb ->select("count(acquis.id) as acquisTotal, matrix.id as matrixId, theme.id as themeId, theme.title")
                                ->groupBy("theme.id");
                            break;
                    }
                    
                    // Filtres
                    $this->filterService->setFormationsFilterInRepository($filter, $qb);

               

        return $qb->getQuery()->getResult();
                    
    }

    /**
     * Utile pour : GRAPHIQUE PROGRESSION GENERALE
     * 
     * Compte le nombre d'acquis présent en fonction de l'entreprise de l'utilisateur (Déterminé dans le fichier : AcquisitionService.php)
     * 
     */
    public function countAcquisByMatrix($listMatrix, $filter){

        $qb = $this ->createQueryBuilder('content');

                    // Granularités
                    $qb ->innerJoin('content.acquisition', 'acquis')
                        ->innerJoin("acquis.theme", "theme")
                        ->innerJoin('theme.skill', 'skill')
                        ->innerJoin("skill.domain", "domain")
                        ->innerJoin('domain.matrix', 'matrix')

                        ->where('matrix.id IN (:listMatrix)')
                        ->setParameter('listMatrix', $listMatrix)

                        ->select("count(acquis.id) as acquisTotal, matrix.id as matrixId, matrix.name as title")
                        ->groupBy("matrix.id");

                    if(!$filter['filterFormationSelected']){
                        $filter['filterFormationSelected'] = '';
                    }
            
                    if($filter['filterFormationSelected']){
                        $qb->andWhere("matrix.id = :filterFormationSelected")
                        ->setParameter('filterFormationSelected', $filter['filterFormationSelected']);
                    }
                            
                    // Filtres
                    $this->filterService->setFormationsFilterInRepository($filter, $qb);
                    

        return $qb->getQuery()->getResult();
                    
    }

    /**
     * @return array - return all contents availables
     */
    public function getAllContents($args, $specificGraphData){
        $qb = $this ->createQueryBuilder('content');

            $qb ->select('content.id as contentId', 'matrix.id as matrixId', "valueAttribute.value as nameCNT", 'acquis.title as nameAcquis', 'domain.title as nameDomain', 'skill.title as nameSkill', 'theme.title as nameTheme');

            // Granularités
            $qb ->join("content.valueAttribute", "valueAttribute")
                ->innerJoin('content.acquisition', 'acquis')
                ->innerJoin("acquis.theme", "theme")
                ->innerJoin('theme.skill', 'skill')
                ->innerJoin("skill.domain", "domain")
                ->innerJoin('domain.matrix', 'matrix');


            $qb ->andWhere('matrix.id = :matrixId')
                ->setParameter(':matrixId', $specificGraphData['setFormationSelected'])
                ->andWhere('content.active = 1')
                ->andWhere('valueAttribute.attribute = 2') // Nom du contenu
                ->andWhere('content.deletedAt IS NULL');

        return $qb->getQuery()->getResult();
    }
}