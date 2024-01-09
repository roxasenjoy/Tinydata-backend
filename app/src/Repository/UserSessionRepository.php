<?php

namespace App\Repository;

use App\Entity\UserSession;
use App\Service\DateService;
use App\Service\GlobalFilterService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserSession|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserSession|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserSession[]    findAll()
 * @method UserSession[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserSessionRepository extends ServiceEntityRepository
{

    private $filterService;
    private $dateService;

    public function __construct(ManagerRegistry $registry,  GlobalFilterService $filterService, DateService $dateService)
    {
        parent::__construct($registry, UserSession::class);
        $this->filterService = $filterService;
        $this->dateService = $dateService;
    }

    /**
     * Détermine le temps passé dans l'aprentissage en fonction des dates que l'utilisateur aura mis en place
     */
    public function getTotalLearningTimePerDate($args)
    {

        $userCompanies = $this->filterService->getAllCompaniesForUser();

        $qb =   $this->createQueryBuilder('us')

            ->select('userClient.id as userClientId', 'us.sessionLength as totalTime', 'us.date')

            // Jointures
            ->join('us.userClient', 'userClient')
            ->join('userClient.user', 'user')
            ->join('userClient.company', 'company');

        //Conditions
        if ($args['organisationsFilter']) {
            $qb->andWhere('company.id IN (:companiesFilterId)')
                ->setParameter(':companiesFilterId', $args['organisationsFilter']);
        }

        if ($userCompanies) {
            $qb->andWhere('company.id IN (:companiesId)')
                ->setParameter(':companiesId', $userCompanies);
        }

        if ($args['beginDate']) {
            $qb->andWhere('us.date >= :beginDate')
                ->setParameter(':beginDate', $args['beginDate']);
        }

        if ($args['endDate']) {
            $qb->andWhere('us.date <= :endDate')
                ->setParameter(':endDate', $args['endDate']);
        }

        if ($args['idUser']) {
            $qb->andWhere('user.id = :userId')
                ->setParameter(':userId', $args['idUser']);
        }


        return $qb->getQuery()->getResult();
    }

    /**
     * Détermine le temps passé dans l'application TinyCoaching en seconde
     */
    public function getTotalLearningTime($args)
    {

        /**
         * Trier le temps de formation en fonction des éléments suivants :
         *  - Filtres (Matrice, domaine, compétence, thème, date)
         *  - Tous les collaborateurs des entreprises possibles (En fonction du rôle de l'utilisateur)
         *  - (CAS PARTILUER) L'utilisateur sélectionné (Avoir des détails précis)
         */

        $userCompanies = $this->filterService->getAllCompaniesForUser();

        $qb =   $this->createQueryBuilder('us')

            ->select('userClient.id as userClientId', 'sum(us.sessionLength) as totalTime')

            // Jointures
            ->join('us.userClient', 'userClient')
            ->join('userClient.user', 'user')
            ->join('userClient.company', 'company');

        //Conditions
        if ($userCompanies) {
            $qb->andWhere('company.id IN (:companiesId)')
                ->setParameter(':companiesId', $userCompanies);
        }

        if ($args['organisationsFilter']) {
            $qb->andWhere('company.id IN (:companiesFilter)')
                ->setParameter(':companiesFilter', $args['organisationsFilter']);
        }


        if ($args['beginDate']) {
            $qb->andWhere('us.date >= :beginDate')
                ->setParameter(':beginDate', $args['beginDate']);
        }

        if ($args['endDate']) {
            $qb->andWhere('us.date <= :endDate')
                ->setParameter(':endDate', $args['endDate']);
        }


        if ($args['idUser']) {
            $qb->andWhere('user.id = :userId')
                ->setParameter(':userId', $args['idUser']);
        }

        $qb->groupBy('userClient.id');

        $sql = $qb->getQuery()->getResult();

        // Boucle pour additionnner tous les éléments
        $sum = 0;
        foreach ($sql as $value) {
            $sum += $value['totalTime'];
        }

        return $sum;
    }


    // Détermine le temps passé dans l'application TinyCoaching pour tous les utilisateurs disponibles
    public function getTotalLearningPerUser($args)
    {

        $userCompanies = $this->filterService->getAllCompaniesForUser();

        $qb =   $this->createQueryBuilder('us')

            ->select('us.id as sessionId', 'us.sessionLength as total', 'userClient.id as ucId', 'parent1.name as parent1Name', 'company.name as companyName', 'user.firstName', 'user.lastName', 'user.email','user.id as userId')

            // Jointures
            ->join('us.userClient', 'userClient')
            ->join('userClient.user', 'user')
            ->join('userClient.company', 'company')
            ->leftJoin('company.parent1', 'parent1')
            
            ;

        //Conditions
        if ($userCompanies) {
            $qb->andWhere('company.id IN (:companiesId)')
                ->setParameter(':companiesId', $userCompanies);
        }

        if ($args['beginDate']) {
            $qb->andWhere('us.date >= :beginDate')
                ->setParameter(':beginDate', $args['beginDate']);
        }

        if ($args['endDate']) {
            $qb->andWhere('us.date <= :endDate')
                ->setParameter(':endDate', $args['endDate']);
        }


        if ($args['idUser']) {
            $qb->andWhere('user.id = :userId')
                ->setParameter(':userId', $args['idUser']);
        }

        if ($args['organisationsFilter']) {
            $qb->andWhere('userClient.company IN (:companies)')
                ->setParameter(':companies', $args['organisationsFilter']);
        }

        $qb->groupBy('us.id', 'user.firstName', 'user.lastName', 'user.email'); 

        return $qb->getQuery()->getResult();
    }

    // Obtenir le nombre de fois où un utilisateur s'est connecté grâce à la session
    public function getNumberOfConnectionsPerUser($args)
    {

        $userCompanies = $this->filterService->getAllCompaniesForUser();

        $qb = $this->createQueryBuilder('us')
            ->select('count(us.id) as totalConnexion, userClient.id as userClientId')

            // Jointures
            ->join('us.userClient', 'userClient')
            ->join('userClient.user', 'user')
            ->join('userClient.company', 'company');;
        //Conditions
        if ($userCompanies) {
            $qb->andWhere('company.id IN (:companiesId)')
                ->setParameter(':companiesId', $userCompanies);
        }

        if ($args['beginDate']) {
            $qb->andWhere('us.date >= :beginDate')
                ->setParameter(':beginDate', $args['beginDate']);
        }

        if ($args['endDate']) {
            $qb->andWhere('us.date <= :endDate')
                ->setParameter(':endDate', $args['endDate']);
        }


        if ($args['idUser']) {
            $qb->andWhere('user.id = :userId')
                ->setParameter(':userId', $args['idUser']);
        }

        if ($args['organisationsFilter']) {
            $qb->andWhere('userClient.company IN (:companies)')
                ->setParameter(':companies', $args['organisationsFilter']);
        }

        $qb->groupBy('userClient.id', 'user.id');

        return $qb->getQuery()->getResult();
    }


    public function getConnections($args, $specificGraphData)
    {

        $beginDate = $this->dateService->getDate($args['beginDate'], $this->dateService::BEGIN);
        $endDate = $this->dateService->getDate($args['endDate'], $this->dateService::END);

        $select = 'COUNT(us.id) as cnt, company.id, company.name, YEAR(us.date) as year, MONTH(us.date) as month';
        $orderBy = "company.id, year, month";

        if ($specificGraphData['precision'] === "DAY") {

            $select .= ', DAY(us.date) as day';
            $orderBy .= ', day';
        }

        if ($specificGraphData['groupByUser'] === "true") {
            $select .= ', user.firstName, user.lastName, user.email, user.id';
            $orderBy .= ', user.lastName, user.firstName';
        }

        $qb = $this->createQueryBuilder('us')
            ->select($select)
            ->join('us.userClient', 'userClient')
            ->join('userClient.company', 'company')
            ->join('userClient.user', 'user')
            ->andWhere('us.date BETWEEN :beginDate AND :endDate')
            ->setParameter('beginDate', $beginDate)
            ->setParameter('endDate', $endDate)
            ->orderBy($orderBy)
            ->addGroupBy('year')
            ->addGroupBy('month');

        if ($specificGraphData['groupByUser'] === "true") {
            $qb->addGroupBy('user.id');
        } else {
            $qb->addGroupBy('company.id');
        }

        if ($specificGraphData['precision'] === "DAY") {
            $qb->addGroupBy('day');
        }

        if (!$args['organisationsFilter']) {
            $userCompanies = $this->filterService->getAllCompaniesForUser();

            if ($userCompanies) {
                $qb->andWhere('userClient.company IN (:company_id)')
                    ->setParameter('company_id', $userCompanies);
            }
        }

        if ($args['organisationsFilter']) {
            $qb->andWhere('userClient.company IN (:company_id)')
                ->setParameter('company_id', $args['organisationsFilter']);
        }

        if ($args['idUser']) {
            $qb
                ->andWhere('user.id = :userId')
                ->setParameter('userId', $args['idUser']);
        }

        return $qb->getQuery()->getResult();
    }

    public function getUserModalData($args, $specificGraphData)
    {

        $userCompanies = $this->filterService->getAllCompaniesForUser();

        $qb = $this->createQueryBuilder('us')
            ->select('company.name as companyName', 'parent1.name as parent1Name', 'user.firstName, user.lastName, user.email, company.name, YEAR(us.date) as year, MONTH(us.date) as month, DAY(us.date) as day, COUNT(us.id) as totalConnexion')

            // Jointures
            ->join('us.userClient', 'userClient')
            ->join('userClient.user', 'user')
            ->join('userClient.company', 'company')
            ->leftJoin('company.parent1', 'parent1')
            ;
        //Conditions
        if ($userCompanies) {
            $qb->andWhere('company.id IN (:companiesId)')
                ->setParameter(':companiesId', $userCompanies);
        }

        if ($args['beginDate']) {
            $qb->andWhere('us.date >= :beginDate')
                ->setParameter(':beginDate', $args['beginDate']);
        }

        if ($args['endDate']) {
            $qb->andWhere('us.date <= :endDate')
                ->setParameter(':endDate', $args['endDate']);
        }

        if ($args['idUser']) {
            $qb->andWhere('user.id = :userId')
                ->setParameter(':userId', $args['idUser']);
        }

        if ($args['organisationsFilter']) {
            $qb->andWhere('userClient.company IN (:companies)')
                ->setParameter(':companies', $args['organisationsFilter']);
        }

        $qb->addGroupBy('year')
            ->addGroupBy('month')
            ->addGroupBy('day')
            ->addGroupBy('user.id');

        $total = count($qb->getQuery()->getResult());

        if ($specificGraphData['_pageIndex']) {
            $nbBeginReturn = ($specificGraphData['_pageIndex'] - 1) * 10;
            $qb->setFirstResult($nbBeginReturn)
                ->setMaxResults(10);
        }


        return array(
            'query' => $qb->getQuery()->getResult(), 
            'countElements' => $total
        );
    }

    public function getTotalConnectionForGraphicConnections($args, $data = null)
    {

        $userCompanies = $this->filterService->getAllCompaniesForUser();

        $qb = $this->createQueryBuilder('us')
            ->select('count(us.id)')
            ->join('us.userClient', 'userClient')
            ->join('userClient.company', 'company')
            ->join('userClient.user', 'user')
            ->andWhere('us.date BETWEEN :beginDate AND :endDate')
            ->setParameter('beginDate', $args['beginDate'])
            ->setParameter('endDate', $args['endDate']);

        if ($args['organisationsFilter']) {
            $qb->andWhere('userClient.company IN (:organisationsFilter)')
                ->setParameter('organisationsFilter', $args['organisationsFilter']);
        }

        if ($args['idUser']) {
            $qb->andWhere('user.id = :id')
                ->setParameter('id', $args['idUser']);
        } else {
            if ($userCompanies) {
                $qb->andWhere('userClient.company IN (:company_id)')
                    ->setParameter('company_id', $userCompanies);
            }
        }

        if ($data) {
            $qb->andWhere('user.id = :id')
                ->setParameter('id', $data);
        }

        return $qb->getQuery()->getResult();
    }
}