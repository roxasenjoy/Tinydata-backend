<?php

namespace App\Repository;

use App\Entity\AnalyticsSkill;
use App\Service\GlobalFilterService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class AnalyticsRepository
 * @package App\Repository
 *
 * @method AnalyticsSkill|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnalyticsSkill|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnalyticsSkill[]    findAll()
 * @method AnalyticsSkill[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnalyticsSkillRepository extends ServiceEntityRepository
{

    /**
     * AnalyticsRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(
        ManagerRegistry $registry,
        GlobalFilterService $filterService)
    {
        parent::__construct($registry, AnalyticsSkill::class);
        $this->filterService = $filterService;
    }


    /**
     * Utile pour : GRAPHIQUE PERFORMANCE
     * 
     * Compte le nombre d'acquis validé
     * 
     * @param granularityType: matrix.id / domain.id / skill.id / theme.id
     */
    public function countAcquisValidatedByGranularity($filter, $granularityType){

        // Si aucune entreprise est filtrée, on se base sur les entreprises accessibles par l'utilisateur
        if(!$filter['organisationsFilter']){
            $filter['organisationsFilter'] = $this->filterService->getAllCompaniesForUser();
        }

        $qb = $this ->createQueryBuilder('analyticsSkill')

                    // Granularités
                    ->innerJoin('analyticsSkill.user_client', 'userClient')
                    ->innerJoin('userClient.user', 'user')
                    ->innerJoin('userClient.company', 'company')
                    ->innerJoin("analyticsSkill.theme", "theme")
                    ->innerJoin('analyticsSkill.skill', 'skill')
                    ->innerJoin("analyticsSkill.domain", "domain")
                    ->innerJoin('analyticsSkill.matrix', 'matrix');
                    
                    // On groupe par la granularité recherchée
                    // + On sélectionne les éléments en fonction du type de données recherché
                    //Choix du group by a réaliser en fonction de ce que l'on veut récup
                    if(!$filter['extended']){
                        switch($granularityType){
                            case 'matrix':
                                $qb ->select("sum(analyticsSkill.nbAcquisitionsValidated) as totalAcquisValidated, matrix.id as matrixId, matrix.name as title")
                                    ->groupBy("matrix.id")
                                    ->orderBy("matrix.name");
                                break;
                            case 'domain':
                                $qb ->select("sum(analyticsSkill.nbAcquisitionsValidated) as totalAcquisValidated, matrix.id as matrixId, domain.id as domainId, domain.title")
                                    ->groupBy("matrix.id, domain.id")
                                    ->orderBy("domain.title");

                                    // On affiche le domaine sélectionné quand l'utilisateur clique sur une formation
                                    // Si ce n'est pas le cas, on affiche tous les éléments
                                    // Utile : Vue enrichie / Dashboard
                                    if(!$filter['displayAllGranularity'] && $filter['matrixID']){
                                        $qb->andWhere('matrix.id = :matrixID')
                                        ->setParameter(':matrixID', $filter['matrixID']);
                                    }
                                break;
                            case 'skill':
                                $qb ->select("sum(analyticsSkill.nbAcquisitionsValidated) as totalAcquisValidated, matrix.id as matrixId, skill.id as skillId, skill.title")
                                    ->groupBy("matrix.id, domain.id, skill.id")
                                    ->orderBy("matrix.name");

                                    if(!$filter['displayAllGranularity'] && $filter['domainID']){
                                        $qb->andWhere('domain.id = :domainID')
                                        ->setParameter(':domainID', $filter['domainID']);
                                    }
                                break;

                            case 'theme':
                                $qb ->select("sum(analyticsSkill.nbAcquisitionsValidated) as totalAcquisValidated, matrix.id as matrixId, theme.id as themeId, theme.title")
                                    ->groupBy("matrix.id, domain.id, skill.id, theme.id")
                                    ->orderBy("matrix.name");

                                    if(!$filter['displayAllGranularity'] && $filter['skillID']){
                                        $qb->andWhere('skill.id = :skillID')
                                        ->setParameter(':skillID', $filter['skillID']);
                                    }
                                break;
                        }
                    }
                    else{
                        switch($granularityType){
                            case 'matrix':
                                $qb ->select("sum(analyticsSkill.nbAcquisitionsValidated) as totalAcquisValidated, matrix.id as matrixId, matrix.name , user.email, user.lastName, user.firstName, company.name as companyName")
                                    ->groupBy("matrix.id, user.id")
                                    ->orderBy("user.lastName, user.firstName, matrix.name");
                                break;
                            case 'domain':
                                $qb ->select("sum(analyticsSkill.nbAcquisitionsValidated) as totalAcquisValidated, matrix.id as matrixId, domain.id as domainId, domain.title, user.email, user.lastName, user.firstName, company.name as companyName")
                                    ->groupBy("matrix.id, domain.id, user.id")
                                    ->orderBy("user.lastName, user.firstName, domain.title");

                                    // On affiche le domaine sélectionné quand l'utilisateur clique sur une formation
                                    // Si ce n'est pas le cas, on affiche tous les éléments
                                    // Utile : Vue enrichie / Dashboard
                                    if(!$filter['displayAllGranularity'] && $filter['matrixID']){
                                        $qb->andWhere('matrix.id = :matrixID')
                                        ->setParameter(':matrixID', $filter['matrixID']);
                                    }

                                break;
                            case 'skill':
                                $qb ->select("sum(analyticsSkill.nbAcquisitionsValidated) as totalAcquisValidated, matrix.id as matrixId, skill.id as skillId, skill.title, user.email, user.lastName, user.firstName, company.name as companyName")
                                    ->groupBy("matrix.id, domain.id,skill.id, user.id")
                                    ->orderBy("user.lastName, user.firstName, skill.title");

                                    if(!$filter['displayAllGranularity'] && $filter['domainID'] ){
                                        $qb->andWhere('domain.id = :domainID')
                                        ->setParameter(':domainID', $filter['domainID']);
                                    }
                                break;

                            case 'theme':

                                $qb ->select("sum(analyticsSkill.nbAcquisitionsValidated) as totalAcquisValidated, matrix.id as matrixId, theme.id as themeId, theme.title, user.email, user.lastName, user.firstName, company.name as companyName")
                                    ->groupBy("matrix.id, domain.id,skill.id,theme.id, user.id")
                                    ->orderBy("user.lastName, user.firstName, theme.title");

                                    if(!$filter['displayAllGranularity'] && $filter['skillID']){
                                        $qb->andWhere('skill.id = :skillID')
                                        ->setParameter(':skillID', $filter['skillID']);
                                    }
                                break;

                            case 'themeExtended':
                                $qb ->select("sum(analyticsSkill.nbAcquisitionsValidated) as totalAcquisValidated, matrix.id as matrixId, theme.id as themeId, theme.title, user.email, user.lastName, user.firstName, company.name as companyName")
                                    ->groupBy("matrix.id, domain.id, skill.id, theme.id, user.id")
                                    ->orderBy("user.lastName, user.firstName, theme.title");

                                    if(!$filter['displayAllGranularity'] && $filter['displayRichData'] && $filter['themeID']){
                                        $qb->andWhere('theme.id = :themeID')
                                        ->setParameter(':themeID', $filter['themeID']);
                                    }
                                break;
                            
                        }
                    }

                    /**
                     * Filtres pour les granularités
                     */
                    $this->filterService->setFormationsFilterInRepository($filter, $qb);

                    /**
                     * Filtres pour un utilisateur en particulier 
                     */
                    if($filter['userInformation']){
                        $qb ->andWhere('user.id = :userId')
                            ->setParameter('userId', $filter['userId']);
                    }

                    /**
                     * Filtre sur les entreprises
                     */
                    if($filter['organisationsFilter']){
                        $qb ->andWhere('company.id IN (:companyId)')
                            ->setParameter('companyId', $filter['organisationsFilter']);
                    };

                    

        return $qb->getQuery()->getResult();
    }


    /**
     * Utile pour : GRAPHIQUE PROGRESSION GENERALE
     * 
     * Compte le nombre d'acquis validé
     * 
     * @param granularityType: matrix.id / domain.id / skill.id / theme.id
     */
    public function countAcquisValidatedByUser($listMatrix, $filter){

        // Si aucune entreprise est filtrée, on se base sur les entreprises accessibles par l'utilisateur
        if(!$filter['organisationsFilter']){
            $filter['organisationsFilter'] = $this->filterService->getAllCompaniesForUser();
        }

        $qb = $this ->createQueryBuilder('analyticsSkill')

                    // Granularités
                    ->innerJoin('analyticsSkill.user_client', 'userClient')
                    ->innerJoin('userClient.user', 'user')
                    ->innerJoin('userClient.company', 'company')
                    ->innerJoin("analyticsSkill.theme", "theme")
                    ->innerJoin('analyticsSkill.skill', 'skill')
                    ->innerJoin("analyticsSkill.domain", "domain")
                    ->innerJoin('analyticsSkill.matrix', 'matrix')
                    
                    // Filtre sur toutes les matrices de l'utilisateur
                    ->where('matrix.id IN (:listMatrix)')
                    ->setParameter('listMatrix', $listMatrix)

                    // Filtre sur l'utilisateur
                    ->andWhere('user.id = :userId')
                    ->setParameter(':userId', $filter['userId'])

                    // On ajoute tous les acquis validés de l'utilisateur pour obtenir son total
                    ->select("sum(analyticsSkill.nbAcquisitionsValidated) as totalAcquisValidated, matrix.id as matrixId, matrix.name as title")
                    ->groupBy("matrix.id")
                    ->orderBy("matrix.name");
                              
                    /**
                     * Filtres pour les granularités
                     */
                    $this->filterService->setFormationsFilterInRepository($filter, $qb);

                    /**
                     * Filtre sur les entreprises
                     */
                    if($filter['organisationsFilter']){
                        $qb ->andWhere('company.id IN (:companyId)')
                            ->setParameter('companyId', $filter['organisationsFilter']);
                    };

                    if(!$filter['filterFormationSelected']){
                        $filter['filterFormationSelected'] = '';
                    }
            
                    if($filter['filterFormationSelected']){
                        $qb->andWhere("matrix.id = :filterFormationSelected")
                        ->setParameter('filterFormationSelected', $filter['filterFormationSelected']);
                    }

                    

        return $qb->getQuery()->getResult();
    }
}
