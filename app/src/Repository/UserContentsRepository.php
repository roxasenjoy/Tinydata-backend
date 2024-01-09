<?php

namespace App\Repository;

use App\Entity\UserContents;
use App\Service\DateService;
use App\Service\GlobalFilterService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\GroupBy;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserContents|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserContents|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserContents[]    findAll()
 * @method UserContents[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserContentsRepository extends ServiceEntityRepository
{
    /**
     * @var GlobalFilterService $filterService
     */
    private $filterService;

    /**
     * @var DateService $dateService
     */
    private $dateService;

    public function __construct(ManagerRegistry $registry, GlobalFilterService $filterService, DateService $dateService)
    {
        parent::__construct($registry, UserContents::class);
        $this->filterService = $filterService;
        $this->dateService = $dateService;
    }

    public function findContent(
        $beginDate,
        $endDate,
        $parcours,
        $entreprise){

        if(!$entreprise){
            $entreprise = $this->filterService->getAllCompaniesForUser();
        }


        $qb =   $this->createQueryBuilder('uc');
        /**
         * Tri en fonction des dates
         */
        $qb     ->select('uc.id, uc.date, company.name')

                ->leftJoin('user.company', 'company')

                ->andWhere('uc.date > :beginDate')
                ->andWhere('uc.date < :endDate')
                ->andWhere('company.deletedAt IS NULL')
                ->setParameter('beginDate', $beginDate)
                ->setParameter('endDate', $endDate);

        /**
         * Tri des entreprises
         */
       if($entreprise){
            $qb->andWhere('company.id IN (:company)')
               ->setParameter('company', $entreprise);
        }

        /**
         * Lancement de la requête
         */
        return
            $qb ->getQuery()
                ->getResult();
    }

    public function getMatrixWithFeedback($companiesFilter, $matrixFilter, $userId, $beginDate, $endDate){

        $companyFilter = $this->filterService->getAllCompaniesForUser();

        $qb = $this->createQueryBuilder('uc');

        $qb->join("uc.userClient", "userClient")
            ->join("userClient.user", "user")
            ->join("userClient.company", "company")
            ->join("uc.content", "content")
            ->join("content.valueAttribute", "valueAttribute")
            ->join("content.acquisition", "acquisition")
            ->join("acquisition.skillLevel", "skillLevel")
            ->join("acquisition.theme", "theme")
            ->join("skillLevel.skill", "skill")
            ->join("skill.domain", "domain")
            ->join("domain.matrix", "matrix")

            ->andWhere('uc.date > :beginDate')
            ->andWhere('uc.date < :endDate')
            ->andWhere('company.deletedAt IS NULL')
            ->andWhere('uc.feedback IN (1,2,3,4)')
            ->setParameter('beginDate', $beginDate)
            ->setParameter('endDate', $endDate);
        
            // dd($matrixFilter);

        // Est ce que l'utilisateur avait déjà sélectionné une formation ? 
        if($matrixFilter){
            $qb ->andWhere('matrix.id IN (:matrixFilter)')
                ->setParameter(':matrixFilter', $matrixFilter);
        }

        // Filtre sur un utilisateur précis
        if($userId){
            $qb ->andWhere('user.id = :idUser')
                ->setParameter(':idUser', $userId);
        }

        if($companyFilter){           
            $qb->andWhere("company.id IN (:companyList)")
            ->setParameter('companyList', $companyFilter);
        }

        if($companiesFilter){           
            $qb->andWhere("company.id IN (:companiesFilter)")
            ->setParameter('companiesFilter', $companiesFilter);
        }

        $qb ->select('matrix.id as matrixID', 'matrix.name', 'uc.feedback')
          ;
        

        
        return $qb->getQuery()->getResult();

    }
    
    public function getMatrixWithPerformance($companiesFilter, $matrixFilter, $userId, $beginDate, $endDate){

        $companyFilter = $this->filterService->getAllCompaniesForUser();

        $beginDate =  $this->dateService->getDate($beginDate, $this->dateService::BEGIN);
        $endDate = $this->dateService->getDate($endDate, $this->dateService::END);

        $qb = $this->createQueryBuilder('userContents')
            ->join("userContents.userClient", "userClient")
            ->join("userClient.user", "user")
            ->join("userClient.company", "company")
            ->join("userContents.content", "content")
            ->join("content.acquisition", "acquisition")
            ->join("acquisition.theme", "theme")
            ->join("theme.skill", "skill")
            ->join("skill.domain", "domain")
            ->join("domain.matrix", "matrix")

            ->andWhere("userContents.isAlreadyValidated = 0")
            ->andWhere("userContents.later is null")
            ->andWhere("userContents.status = 1")
            ->andWhere('company.deletedAt IS NULL')
            ->andWhere('userContents.date > :beginDate')
            ->andWhere('userContents.date < :endDate')
            ->setParameter('beginDate', $beginDate)
            ->setParameter('endDate', $endDate)

            ->select('matrix.id as matrixID', 'matrix.name')
            ->groupBy('matrix.id')
            ->orderBy('matrix.id'); 
            


            if($matrixFilter){
                $qb ->andWhere('matrix.id IN (:matrixFilter)')
                    ->setParameter(':matrixFilter', $matrixFilter);
            }

            if($userId){
                $qb ->andWhere('user.id IN (:userFilter)')
                    ->setParameter(':userFilter', $userId);
            }

            if($companyFilter){           
                $qb->andWhere("company.id IN (:companyList)")
                ->setParameter('companyList', $companyFilter);
            }

            if($companiesFilter){           
                $qb->andWhere("company.id IN (:companiesFilter)")
                ->setParameter('companiesFilter', $companiesFilter);
            }

        return $qb->getQuery()->getResult();

    }

    /**
     * Graphique PERFORMANCE
     */
    public function getValidatedContentByGranularityForGeneralProgression($granularityLevel, $args, $clickedGranularity = null){
        if(!$args['organisationsFilter']){
            $args['organisationsFilter'] = $this->filterService->getAllCompaniesForUser();
        }
        $beginDate =  $this->dateService->getDate($args['beginDate'], $this->dateService::BEGIN);
        $endDate = $this->dateService->getDate($args['endDate'], $this->dateService::END);
        
        $qb = $this->createQueryBuilder('userContents')
        ->join("userContents.userClient", "userClient")
        ->join("userClient.user", "user")
        ->join("userClient.company", "company")
        ->join("userContents.content", "content")
        ->join("content.acquisition", "acquisition")
        ->join("acquisition.theme", "theme")
        ->join("theme.skill", "skill")
        ->join("skill.domain", "domain")
        ->join("domain.matrix", "matrix")

        //Conditions pour qu'un contenu soit considéré validé
        ->andWhere("userContents.isAlreadyValidated = 0")
        ->andWhere("userContents.later is null")
        ->andWhere("userContents.status = 1")
        ->andWhere('userContents.date > :beginDate')
        ->andWhere('userContents.date < :endDate')
        ->setParameter('beginDate', $beginDate)
        ->setParameter('endDate', $endDate);

        //Choix du group by a réaliser en fonction de ce que l'on veut récup
        if(!$args['extended']){
            switch($granularityLevel){
                case $this->filterService::DEPTH_MATRIX:
                    $qb->select("count(userContents.id) as total, matrix.id, matrix.name")
                    ->groupBy("matrix.id")
                    ->orderBy("matrix.name");
                    break;
                case $this->filterService::DEPTH_DOMAIN:
                    $qb->select("count(userContents.id) as total, matrix.id as matrixID, domain.id, domain.title")
                    ->groupBy("domain.id")
                    ->orderBy("matrix.name");
                    break;
                case $this->filterService::DEPTH_SKILL:
                    $qb->select("count(userContents.id) as total, matrix.id as matrixID, skill.id, skill.title")
                    ->groupBy("skill.id")
                    ->orderBy("matrix.name");
                    break;
                case $this->filterService::DEPTH_THEME:
                    $qb->select("count(userContents.id) as total, matrix.id as matrixID, skill.id, theme.id, theme.title")
                    ->groupBy("theme.id")
                    ->orderBy("matrix.name");
                    break;
            }
        }
        else{
            switch($granularityLevel){
                case $this->filterService::DEPTH_MATRIX:
                    $qb->select("count(userContents.id) as total, matrix.id, matrix.name, user.email, user.lastName, user.firstName, company.name as companyName")
                    ->groupBy("matrix.id, user.id")
                    ->orderBy("user.lastName, user.firstName, matrix.name");
                    break;
                case $this->filterService::DEPTH_DOMAIN:
                    $qb->select("count(userContents.id) as total, matrix.id as matrixID, domain.id, domain.title, user.email, user.lastName, user.firstName, company.name as companyName")
                    ->groupBy("domain.id, user.id")
                    ->orderBy("user.lastName, user.firstName, domain.title");
                    break;
                case $this->filterService::DEPTH_SKILL:
                    $qb->select("count(userContents.id) as total, matrix.id as matrixID, skill.id, skill.title, user.email, user.lastName, user.firstName, company.name as companyName")
                    ->groupBy("skill.id, user.id")
                    ->orderBy("user.lastName, user.firstName, skill.title");
                    break;
                case $this->filterService::DEPTH_THEME:
                    $qb->select("count(userContents.id) as total, matrix.id as matrixID, skill.id, theme.id, theme.title, user.email, user.lastName, user.firstName, company.name as companyName")
                    ->groupBy("theme.id, user.id")
                    ->orderBy("user.lastName, user.firstName, theme.title");
                    break;
            }
        }

        if($args['userId']){
            $qb->andWhere("user.id = :user")
            ->setParameter("user", $args['userId']);
        }

        if($args['organisationsFilter']){
            $qb->andWhere("userClient.company in (:companies)")
            ->setParameter("companies", $args['organisationsFilter']);
        }

        if($clickedGranularity){
            switch($granularityLevel){
                case $this->filterService::DEPTH_MATRIX:
                    //Nothing, this can't happend
                    break;
                case $this->filterService::DEPTH_DOMAIN:
                    $qb->andWhere("matrix.id = :clickedGranularity");
                    break;
                case $this->filterService::DEPTH_SKILL:
                    $qb->andWhere("domain.id = :clickedGranularity");
                    break;
                case $this->filterService::DEPTH_THEME:
                    $qb->andWhere("skill.id = :clickedGranularity");
                    break;
            }
            $qb->setParameter("clickedGranularity", $clickedGranularity);
        }

        $this->filterService->setFormationsFilterInRepository($args, $qb); // Set les filtres pour Matrices, Domaines, Compétences, Thèmes

        return $qb->getQuery()->getResult(); 
    }

    /**
     * Graphique PERFORMANCE
     */
    public function getValidatedContentByGranularity($args, $specificGraphData){

        if(!$args['organisationsFilter']){
            $args['organisationsFilter'] = $this->filterService->getAllCompaniesForUser();
        }

        $beginDate =  $this->dateService->getDate($args['beginDate'], $this->dateService::BEGIN);
        $endDate = $this->dateService->getDate($args['endDate'], $this->dateService::END);
        
        $qb = $this->createQueryBuilder('userContents')
        ->join("userContents.userClient", "userClient")
        ->join("userClient.user", "user")
        ->join("userClient.company", "company")
        ->join("userContents.content", "content")
        ->join("content.acquisition", "acquisition")
        ->join("acquisition.theme", "theme")
        ->join("theme.skill", "skill")
        ->join("skill.domain", "domain")
        ->join("domain.matrix", "matrix")

        //Conditions pour qu'un contenu soit considéré validé
        ->andWhere("userContents.isAlreadyValidated = 0")
        ->andWhere("userContents.later is null")
        ->andWhere("userContents.status = 1")
        ->andWhere('userContents.date > :beginDate')
        ->andWhere('userContents.date < :endDate')
        ->setParameter('beginDate', $beginDate)
        ->setParameter('endDate', $endDate);

        if(!$args['filterFormationSelected']){
            $args['filterFormationSelected'] = '';
        }

        if($args['filterFormationSelected']){
            $qb->andWhere("matrix.id = :filterFormationSelected")
            ->setParameter('filterFormationSelected', $args['filterFormationSelected']);
        }

        //Choix du group by a réaliser en fonction de ce que l'on veut récup
        if(!$args['extended']){
            switch($specificGraphData["nameDepth"]){
                case 'matrix':
                    $qb->select("count(userContents.id) as total, matrix.id, matrix.name")
                    ->groupBy("matrix.id")
                    ->orderBy("matrix.name");
                    break;
                case 'domain':
                    $qb->select("count(userContents.id) as total, matrix.id as matrixID, domain.id, domain.title")
                    ->groupBy("domain.id")
                    ->orderBy("matrix.name");
                    break;
                case 'skill':
                    $qb->select("count(userContents.id) as total, matrix.id as matrixID, skill.id, skill.title")
                    ->groupBy("skill.id")
                    ->orderBy("matrix.name");
                    break;
                case 'theme':
                    $qb->select("count(userContents.id) as total, matrix.id as matrixID, skill.id, theme.id, theme.title")
                    ->groupBy("theme.id")
                    ->orderBy("matrix.name");
                    break;
            }
        }
        else{
            switch($specificGraphData["nameDepth"]){
                case 'matrix':
                    $qb->select("count(userContents.id) as total, matrix.id, matrix.name, user.email, user.lastName, user.firstName, company.name as companyName")
                    ->groupBy("matrix.id, user.id")
                    ->orderBy("user.lastName, user.firstName, matrix.name");
                    break;
                case 'domain':
                    $qb->select("count(userContents.id) as total, matrix.id as matrixID, domain.id, domain.title, user.email, user.lastName, user.firstName, company.name as companyName")
                    ->groupBy("domain.id, user.id")
                    ->orderBy("user.lastName, user.firstName, domain.title");
                    break;
                case 'skill':
                    $qb->select("count(userContents.id) as total, matrix.id as matrixID, skill.id, skill.title, user.email, user.lastName, user.firstName, company.name as companyName")
                    ->groupBy("skill.id, user.id")
                    ->orderBy("user.lastName, user.firstName, skill.title");
                    break;
                case 'theme':
                    $qb->select("count(userContents.id) as total, matrix.id as matrixID, skill.id, theme.id, theme.title, user.email, user.lastName, user.firstName, company.name as companyName")
                    ->groupBy("theme.id, user.id")
                    ->orderBy("user.lastName, user.firstName, theme.title");
                    break;
            }
        }

        if($args['idUser']){
            $qb->andWhere("user.id = :user")
            ->setParameter("user", $args['idUser']);
        }

        if($args['organisationsFilter']){
            $qb->andWhere("userClient.company in (:companies)")
            ->setParameter("companies", $args['organisationsFilter']);
        }

        if($specificGraphData['depthClickedID']){
            switch($specificGraphData['nameDepth']){
                case 'matrix':
                    //Nothing, this can't happend
                    break;
                case 'domain':
                    $qb->andWhere("matrix.id = :clickedGranularity");
                    break;
                case 'skill':
                    $qb->andWhere("domain.id = :clickedGranularity");
                    break;
                case 'theme':
                    $qb->andWhere("skill.id = :clickedGranularity");
                    break;
            }
            $qb->setParameter("clickedGranularity", $specificGraphData['depthClickedID']);
        }

        $this->filterService->setFormationsFilterInRepository($args, $qb); // Set les filtres pour Matrices, Domaines, Compétences, Thèmes

        return $qb->getQuery()->getResult(); 
    }

    /**
     * Obtenir tous les contenus validés en fonction de la formation sélectionnée
     */
    public function getAllContentsValidated($args, $specificGraphData){

        $userCompanies = $this->filterService->getAllCompaniesForUser();
        
        $qb = $this->createQueryBuilder('userContents')

        ->select('userContents.status', 'userContents.id as userContentsId', 'matrix.id as matrixId', 'userClient.id as userClientId', "valueAttribute.value as nameCNT", "content.id as contentId", 'acquisition.title as nameAcquis', 'domain.title as nameDomain', 'skill.title as nameSkill', 'theme.title as nameTheme')

        ->join("userContents.userClient", "userClient")
        ->join("userClient.user", "user")
        ->join("userClient.company", "company")
        ->join("userContents.content", "content")
        ->join("content.valueAttribute", "valueAttribute")
        ->join("content.acquisition", "acquisition")
        ->join("acquisition.skillLevel", "skillLevel")
        ->innerJoin("acquisition.theme", "theme")
        ->join("skillLevel.skill", "skill")
        ->join("skill.domain", "domain")
        ->join("domain.matrix", "matrix")
        

        //Conditions pour qu'un contenu soit considéré validé
        ->andWhere("userContents.isAlreadyValidated = 0")
        ->andWhere("userContents.later is null")
        ->andWhere('content.active = 1')
        ->andWhere("valueAttribute.attribute = 2") // Nom du contenu
        ->andWhere('content.deletedAt IS NULL')

        // Formation sélectionnée
        ->andWhere("matrix.id = :idMatrix")
        ->setParameter('idMatrix', $specificGraphData['setFormationSelected'])

        ->andWhere('userContents.date > :beginDate')
        ->andWhere('userContents.date < :endDate')
        ->setParameter('beginDate', $args['beginDate'])
        ->setParameter('endDate', $args['endDate'])
        
        ->orderBy('userContents.id', 'ASC')
        ;

        if($userCompanies){
            $qb->andWhere('userClient.company IN (:organisationsFilter)')
            ->setParameter('organisationsFilter', $userCompanies);
        }

        if($args['organisationsFilter']){
            $qb->andWhere('userClient.company IN (:organisationsFilter)')
            ->setParameter('organisationsFilter', $args['organisationsFilter']);
        }

        if($args['idUser']){
            $qb->andWhere('user.id = :idUser')
                ->setParameter(':idUser', $args['idUser']);
        }

        $this->filterService->setFormationsFilterInRepository($args, $qb);

        return $qb->getQuery()->getResult();
    }

    public function getFeedbacks($args, $specificGraphData){

        $userCompanies = $this->filterService->getAllCompaniesForUser();

        $qb = $this->createQueryBuilder('uc');

        $qb->join("uc.userClient", "userClient")
        ->join("userClient.user", "user")
        ->join("userClient.company", "company")
        ->join("uc.content", "content")
        ->join("content.valueAttribute", "valueAttribute")
        ->join("content.acquisition", "acquisition")
        ->join("acquisition.skillLevel", "skillLevel")
        ->join("acquisition.theme", "theme")
        ->join("skillLevel.skill", "skill")
        ->join("skill.domain", "domain")
        ->join("domain.matrix", "matrix")

        ->andWhere('uc.date > :beginDate')
        ->andWhere('uc.date < :endDate')
        ->setParameter('beginDate', $args['beginDate'])
        ->setParameter('endDate', $args['endDate'])
        ;

        // Est ce que l'utilisateur avait déjà sélectionné une formation ? 
        if($specificGraphData['setFormationSelected']){
            $qb ->andWhere('matrix.id = :matrixSelected')
                ->setParameter(':matrixSelected', $specificGraphData['setFormationSelected']);
        }

        if($userCompanies){
            $qb ->andWhere('company.id IN (:userCompanies)')
                ->setParameter(':userCompanies', $userCompanies);
        }

        // Filtre sur un utilisateur précis
        if($args['idUser']){
            $qb ->andWhere('user.id = :idUser')
                ->setParameter(':idUser', $args['idUser']);
        }
        
        switch($specificGraphData['formationZoom']){

            // Matrices -> Domaine
            case 1:
                $qb ->andWhere('matrix.id = :idGranularity')
                    ->setParameter(':idGranularity', $specificGraphData['idGranularity']);
                
                $qb->select('domain.id as idGranularityFeedback', 'domain.title as name','uc.feedback','count(uc.feedback) as total')
                ->groupBy('domain.id', 'uc.feedback')
                ->orderBy('domain.id'); 

                break;

            // Domaines -> Compétences
            case 2:
                $qb ->andWhere('domain.id = :idGranularity')
                    ->setParameter(':idGranularity', $specificGraphData['idGranularity']);
            
                $qb->select('skill.id as idGranularityFeedback', 'skill.title as name','uc.feedback','count(uc.feedback) as total')
                ->groupBy('skill.id', 'uc.feedback')
                ->orderBy('skill.id'); 
                break;

            // Compétences -> Thèmes
            case 3:
                $qb ->andWhere('skill.id = :idGranularity')
                    ->setParameter(':idGranularity', $specificGraphData['idGranularity']);
                    
                $qb->select('theme.id as idGranularityFeedback', 'theme.title as name','uc.feedback','count(uc.feedback) as total')
                ->groupBy('theme.id', 'uc.feedback')
                ->orderBy('theme.id'); 
                break;

            // Thèmes -> Acquisition
            case 4:
                $qb->andWhere('theme.id = :idGranularity')
                    ->setParameter(':idGranularity', $specificGraphData['idGranularity']);
                    
                $qb ->select('acquisition.id as idGranularityFeedback', 'acquisition.title as name','uc.feedback','count(uc.feedback) as total')
                    ->groupBy('acquisition.id', 'uc.feedback')
                    ->orderBy('acquisition.id'); 
                break;

            // Acquis -> Contenu
            case 5:
                $qb->andWhere('acquisition.id = :idGranularity')
                    ->setParameter(':idGranularity', $specificGraphData['idGranularity']);
                    
                $qb ->select('content.id as idGranularityFeedback', 'valueAttribute.value as name','uc.feedback','count(uc.feedback) as total')
                    ->andWhere("valueAttribute.attribute = 2")
                    ->groupBy('content.id', 'valueAttribute.id', 'uc.feedback')
                    ->orderBy('content.id'); 
                    break;

            // Contenus- > Le monde de narnia
            default:
                    $qb->select('matrix.id as idGranularityFeedback', 'matrix.name as name','uc.feedback','count(uc.feedback) as total')
                    ->groupBy('matrix.id', 'uc.feedback')
                    ->orderBy('matrix.id'); 
                break;
        }

        $this->filterService->setFormationsFilterInRepository($args, $qb); // Filtres les formations sélectionnées par l'utilisateur
        $this->filterService->setOrganisationsFilterInRepository($args, $qb); // Filtres les entreprises sélectionnées par l'utilisateur
        
        return $qb->getQuery()->getResult();

    }

    public function getMatrixWithFeedbackMessage($companiesFilter, $matrixFilter, $userId, $beginDate, $endDate){

        $companyFilter = $this->filterService->getAllCompaniesForUser();

        $qb = $this->createQueryBuilder('uc');

        $qb ->join("uc.userClient", "userClient")
            ->join("userClient.user", "user")
            ->join("userClient.company", "company")
            ->join("uc.content", "content")
            ->join("content.valueAttribute", "valueAttribute")
            ->join("content.acquisition", "acquisition")
            ->join("acquisition.skillLevel", "skillLevel")
            ->join('skillLevel.level', 'level')
            ->join("acquisition.theme", "theme")
            ->join("skillLevel.skill", "skill")
            ->join("skill.domain", "domain")
            ->join("domain.matrix", "matrix")

            ->andWhere('content.active = 1')
            ->andWhere('valueAttribute.attribute = 2') // Nom du contenu
            ->andWhere('uc.contribution IS NOT NULL')
            ->andWhere('content.deletedAt IS NULL')
            ->andWhere('company.deletedAt IS NULL')
            ->andWhere('uc.date > :beginDate')
            ->andWhere('uc.date < :endDate')
            ->setParameter('beginDate', $beginDate)
            ->setParameter('endDate', $endDate)

            ->select('matrix.id as matrixID', 'matrix.name')
            ->groupBy('matrix.id')
            ->orderBy('matrix.id'); 


            if($matrixFilter){
                $qb ->andWhere('matrix.id IN (:matrixFilter)')
                    ->setParameter(':matrixFilter', $matrixFilter);
            }

            if($userId){
                $qb ->andWhere('user.id IN (:userFilter)')
                    ->setParameter(':userFilter', $userId);
            }

            if($companyFilter){           
                $qb->andWhere("company.id IN (:companyList)")
                ->setParameter('companyList', $companyFilter);
            }

            if($companiesFilter){           
                $qb->andWhere("company.id IN (:companiesFilter)")
                ->setParameter('companiesFilter', $companiesFilter);
            }

        return $qb->getQuery()->getResult();

    }

    public function getFeedbacksMessage($args, $specificGraphData, $getTotal = false){


        $companyFilter = $this->filterService->getAllCompaniesForUser();

        $qb = $this->createQueryBuilder('uc');

        if(!$getTotal){
            $qb->select(
                'content.contentCode as code', 
                'content.id as idGranularity', 
                'level.rank as levelContent', 
                'user.firstName as firstName', 
                'user.lastName as lastName',
                'user.email as email', 
                'company.name as companyName',
                'parent1.name as parent1Name,
                parent2.name as parent2Name,
                parent3.name as parent3Name,
                parent4.name as parent4Name,
                parent5.name as parent5Name,
                parent6.name as parent6Name,
                parent7.name as parent7Name,
                parent8.name as parent8Name,
                parent9.name as parent9Name,
                parent10.name parent10Name',
                'uc.date as date',
                'uc.contribution as message',
                'valueAttribute.value as nameContent', 
                'matrix.id as matrixId',
                'matrix.name as nameMatrix',
                'domain.title as nameDomain',
                'skill.title as nameSkill',
                'theme.title as nameTheme',
                'acquisition.title as nameAcquis',
                );       
        }
        

        $qb ->join("uc.userClient", "userClient")
            ->join("userClient.user", "user")
            ->join("userClient.company", "company")
            ->join("uc.content", "content")
            ->join("content.valueAttribute", "valueAttribute")
            ->join("content.acquisition", "acquisition")
            ->join("acquisition.skillLevel", "skillLevel")
            ->join('skillLevel.level', 'level')
            ->join("acquisition.theme", "theme")
            ->join("skillLevel.skill", "skill")
            ->join("skill.domain", "domain")
            ->join("domain.matrix", "matrix")

            ->andWhere('uc.date > :beginDate')
            ->andWhere('uc.date < :endDate')
            ->setParameter('beginDate', $args['beginDate'])
            ->setParameter('endDate', $args['endDate'])

            ->andWhere('content.active = 1')
            ->andWhere('valueAttribute.attribute = 2') // Nom du contenu
            ->andWhere('uc.contribution IS NOT NULL')
            ->andWhere('content.deletedAt IS NULL')

            
            
        ;

        for($depth = 1; $depth <= 10; $depth++){
            $qb->leftJoin('company.parent'. $depth, 'parent'. $depth);
        }

        // Comptes le nombre total de commentaire par formation
        if($getTotal){
            $this->getTotalComments($qb, $specificGraphData);
        }

        if($args['organisationsFilter']){
            $qb->andWhere('company.id IN (:organisationsFilter)');
            $qb->setParameter(':organisationsFilter', $args['organisationsFilter']);
                
        }

        if($companyFilter){           
            $qb->andWhere("company.id IN (:companyList)")
            ->setParameter('companyList', $companyFilter);
        }

        // Est ce que l'utilisateur avait déjà sélectionné une formation ? 
        if($specificGraphData['setFormationSelected']){
            $qb ->andWhere('matrix.id = :matrixSelected')
                ->setParameter(':matrixSelected', $specificGraphData['setFormationSelected']);
        }

        // Filtre sur un utilisateur précis
        if($args['idUser']){
            $qb ->andWhere('user.id = :idUser')
                ->setParameter(':idUser', $args['idUser']);
        }
        
        switch($specificGraphData['formationZoom']){

                // Matrices
                case 1:
                    $qb ->andWhere('matrix.id = :idGranularity')
                        ->setParameter(':idGranularity', $specificGraphData['idGranularity']);
                    break;
    
                // Domaines
                case 2:
                    $qb ->andWhere('domain.id = :idGranularity')
                        ->setParameter(':idGranularity', $specificGraphData['idGranularity']); 
                    break;
    
                // Compétences
                case 3:
                    $qb ->andWhere('skill.id = :idGranularity')
                        ->setParameter(':idGranularity', $specificGraphData['idGranularity']);
                        
                    break;
    
                // Thèmes
                case 4:
                    $qb->andWhere('theme.id = :idGranularity')
                        ->setParameter(':idGranularity', $specificGraphData['idGranularity']);
                    break;
                
                // Acquis
                case 5:
                    $qb ->andWhere('acquisition.id = :idGranularity')
                        ->setParameter(':idGranularity', $specificGraphData['idGranularity']);
                    break;
            }

            
        $this->filterService->setFormationsFilterInRepository($args, $qb); // Filtres les formations sélectionnées par l'utilisateur
            
        return $qb->getQuery()->getResult();
    }

    private function getTotalComments($qb, $specificGraphData){
        switch($specificGraphData['formationZoom']){

            // Matrices -> Domaine
            case 1:
                $qb ->andWhere('matrix.id = :idGranularity')
                    ->setParameter(':idGranularity', $specificGraphData['idGranularity']);
                
                $qb->select('domain.id as idGranularityFeedback', 'domain.title as name','count(uc.contribution) as total')
                ->groupBy('domain.id')
                ->orderBy('domain.id'); 

                break;

            // Domaines -> Compétences
            case 2:
                $qb ->andWhere('domain.id = :idGranularity')
                    ->setParameter(':idGranularity', $specificGraphData['idGranularity']);
            
                $qb->select('skill.id as idGranularityFeedback', 'skill.title as name','count(uc.contribution) as total')
                ->groupBy('skill.id')
                ->orderBy('skill.id'); 
                break;

            // Compétences -> Thèmes
            case 3:
                $qb ->andWhere('skill.id = :idGranularity')
                    ->setParameter(':idGranularity', $specificGraphData['idGranularity']);
                    
                $qb->select('theme.id as idGranularityFeedback', 'theme.title as name','count(uc.contribution) as total')
                ->groupBy('theme.id')
                ->orderBy('theme.id'); 
                break;

            // Thèmes -> Acquisition
            case 4:
                $qb->andWhere('theme.id = :idGranularity')
                    ->setParameter(':idGranularity', $specificGraphData['idGranularity']);
                    
                $qb ->select('acquisition.id as idGranularityFeedback', 'acquisition.title as name','count(uc.contribution) as total')
                    ->groupBy('acquisition.id')
                    ->orderBy('acquisition.id'); 
                break;

            // Acquis -> Contenu
            case 5:
                $qb->andWhere('acquisition.id = :idGranularity')
                    ->setParameter(':idGranularity', $specificGraphData['idGranularity']);
                    
     
                $qb ->select('content.id as idGranularityFeedback', 'valueAttribute.value as name','count(uc.contribution) as total')
                    ->andWhere("valueAttribute.attribute = 2")
                    ->groupBy('content.id', 'valueAttribute.id')
                    ->orderBy('content.id'); 
                    break;

            // Contenus- > Le monde de narnia
            default:
                    $qb->select('matrix.id as idGranularityFeedback', 'matrix.name as name','count(uc.contribution) as total')
                    ->groupBy('matrix.id')
                    ->orderBy('matrix.id'); 
                break;
        }
    }
}
