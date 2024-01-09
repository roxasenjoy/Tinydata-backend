<?php

namespace App\Repository;

use App\Entity\UserSessionDetails;
use App\Service\GlobalFilterService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserSessionDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserSessionDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserSessionDetails[]    findAll()
 * @method UserSessionDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserSessionDetailsRepository extends ServiceEntityRepository
{

    private $filterService;

    public function __construct(ManagerRegistry $registry, GlobalFilterService $filterService)
    {
        parent::__construct($registry, UserSessionDetails::class);
        $this->filterService = $filterService;
    }

    /**
     * Détermine le temps passé dans l'application TinyCoaching pour tous les utilisateurs disponibles
     * Si la différence entre le temps du début et le temps de fin est de 1j, on affiche le temps de chaque session avec la dernière intéraction
     */
    public function getDetailsTotalLearningPerUser($args)
    {

        $allCompaniesId = $this->filterService->getAllCompaniesForUser();

        $qb =   $this->createQueryBuilder('usd')

            ->select('us.id as sessionId', 'userClient.id as userClientId', 'usd.type as lastInteraction', 'usd.length as totalTime' )

            // Jointures
            ->join('usd.userSession', 'us')
            ->join('us.userClient', 'userClient')
            ->join('userClient.user', 'user')
            ->join('userClient.company', 'company')
            ->join('company.matrices', 'matrix')
            ->join('matrix.domains', 'domain')
            ->join('domain.skills', 'skill')
            ->join('skill.themes', 'theme')

            ->groupBy('us.id')
            
            ;

        //Conditions
        if($allCompaniesId){
            $qb->andWhere('company.id IN (:companiesId)')
                ->setParameter(':companiesId', $allCompaniesId);
        }

        if($args['idUser']){
            $qb ->andWhere('user.id = :userId')
                ->setParameter(':userId', $args['idUser']);
        }

        $this->filterService->setFormationsFilterInRepository($args, $qb);
        $this->filterService->setOrganisationsFilterInRepository($args, $qb);
        $this->filterService->setDateFilterInRepository($args, $qb);

        return $qb->getQuery()->getResult();
    
    }

    public function getLastInteraction($userClientId){

        return $this->createQueryBuilder('usd')
        ->select('usd.type')
        ->join('usd.userSession ', 'us')
        ->join('us.userClient', 'uc')
        ->where('uc.id = :userClientId')
        ->setParameter(':userClientId', $userClientId)
        ->orderBy('usd.date', 'DESC')
        ->setMaxResults(1)
        ->getQuery()
        ->getResult();
    }
}
