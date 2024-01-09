<?php

namespace App\Repository;

use App\Entity\UserClient;
use App\Service\GlobalFilterService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserClient|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserClient|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserClient[]    findAll()
 * @method UserClient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserClientRepository extends ServiceEntityRepository
{
    private $filterService;
    const maxDepth = 10;

    public function __construct(ManagerRegistry $registry, GlobalFilterService $filterService)
    {
        parent::__construct($registry, UserClient::class);
        $this->filterService = $filterService;
    }

    public function getEmailOpenAccounts($args)
    {

        $companies = $this->filterService->getAllCompaniesForUser();

        $qb = $this->createQueryBuilder('userClient')
            ->select(
                'userClient.first_connection as date',
                'user.firstName',
                'user.lastName',
                'user.email',
                'user.roles',
                'matrix.name as formationName',
                'company.name as companyName',
                'company.id as companyId',
                'matrix.id as formationId',
                'parent1.id as parent1Id,
                parent2.id as parent2Id,
                parent3.id as parent3Id,
                parent4.id as parent4Id,
                parent5.id as parent5Id,
                parent6.id as parent6Id,
                parent7.id as parent7Id,
                parent8.id as parent8Id,
                parent9.id as parent9Id,
                parent10.id as parent10Id,
                parent1.name as parent1Name,
                parent2.name as parent2Name,
                parent3.name as parent3Name,
                parent4.name as parent4Name,
                parent5.name as parent5Name,
                parent6.name as parent6Name,
                parent7.name as parent7Name,
                parent8.name as parent8Name,
                parent9.name as parent9Name,
                parent10.name as parent10Name'
            )
            ->join('userClient.user', 'user')
            ->join('userClient.company', 'company')
            ->join('company.matrices', 'matrix')

            ->andWhere('userClient.inscriptionDate > :beginDate')
            ->andWhere('userClient.inscriptionDate < :endDate')

            ->setParameter('beginDate', $args['beginDate'])
            ->setParameter('endDate', $args['endDate'])

            ->addOrderBy('matrix.name')
            ->addOrderBy('user.email');

        for ($depth = 1; $depth <= self::maxDepth; $depth++) {
            $qb->leftJoin('company.parent' . $depth, 'parent' . $depth);
        }

        if ($companies) {
            $qb->andWhere('company.id IN (:company)')
                ->setParameter('company', $companies);
        }
        if ($args['matrixFilter']) {
            $qb->andWhere('matrix.id IN (:matrixFilter)')
                ->setParameter(':matrixFilter', $args['matrixFilter']);
        }

        if ($args['organisationsFilter']) {
            $qb->andWhere('company.id IN (:organisationsFilter)')
                ->setParameter(':organisationsFilter', $args['organisationsFilter']);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Récupère le nombre d'utilisateurs qui sont dans le même entreprise
     * @param $idCompany
     * @return int|mixed|string
     */
    public function getUsersCompanies($args, $specificGraphData)
    {

        $userCompanies = $this->filterService->getAllCompaniesForUser();

        $qb = $this->createQueryBuilder('user_client')
            ->join('user_client.user', 'user')
            ->join('user_client.company', 'company')
            ->leftJoin('company.parent1', 'parent1')
            ->join('company.contracts', 'contracts');

        if ($userCompanies) {
            $qb->andWhere('company.id IN (:userCompanies)')
                ->setParameter('userCompanies', $userCompanies);
        }

        if ($args['organisationsFilter']) {
            $qb->andWhere('company.id IN (:company)')
                ->setParameter('company', $args['organisationsFilter']);
        }

        if ($args['idUser']) {
            $qb->andWhere('user.id IN (:user)')
                ->setParameter('user', $args['idUser']);
        }

        if ($specificGraphData === "false") {
            $qb->select(' count(company.id) as number_users');

            return (int)$qb->getQuery()->getSingleScalarResult();
        } else {
            /* Nous avons besoin de toutes les informations des users actifs */
            if ($specificGraphData['_pageIndex']) {
                $nbBeginReturn = ($specificGraphData['_pageIndex'] - 1) * 10;
                $qb->setFirstResult($nbBeginReturn)
                    ->setMaxResults(10);
            }

            $qb->select(
                '
                user.firstName, 
                user.lastName, 
                user.email, 
                user.enabled,
                user_client.inscriptionDate,
                user_client.first_connection as firstConnection', 
                'company.name as companyName',
                'parent1.name as parent1Name',
                'contracts.current',
                'contracts.dateFrom as contractDateBegin',
                'contracts.dateTo as contractDateEnd'
            )
                ->orderBy('user_client.inscriptionDate', 'DESC');

            return $qb->getQuery()->getResult();
        }
    }


    /**
     * Récupère le nombre d'utilisateurs qui sont inscrit (Onboardé)
     * @param $idCompany
     */
    public function getUsersOnBorder($args)
    {


        $userCompanies = $this->filterService->getAllCompaniesForUser();

        $qb = $this->createQueryBuilder('user_client')
            ->select(' count(company.id) as users_active')
            ->join('user_client.user', 'user')
            ->join('user_client.company', 'company')

            ->andWhere('user_client.inscriptionDate > :beginDate')
            ->andWhere('user_client.inscriptionDate < :endDate')

            ->setParameter('beginDate', $args['beginDate'])
            ->setParameter('endDate', $args['endDate']);

        if ($userCompanies) {
            $qb->andWhere('company.id IN (:company)')
                ->setParameter('company', $userCompanies);
        }

        if ($args['organisationsFilter']) {
            $qb->andWhere('company.id IN (:company)')
                ->setParameter('company', $args['organisationsFilter']);
        }

        if ($args['idUser']) {
            $qb->andWhere('user.id IN (:user)')
                ->setParameter('user', $args['idUser']);
        }


        return (int)$qb->getQuery()->getSingleScalarResult();
    }


    public function getParcoursCompanies($companies = null, $user = null)
    {
        if (!$companies) {
            $companies = $this->filterService->getAllCompaniesForUser();
        }
        $qb = $this->createQueryBuilder('user_client')
            ->select(' user_client.onBoardingProfile')
            ->join('user_client.user', 'user')
            ->leftJoin('user_client.company', 'company');

        if ($companies) {
            $qb->where('company.id IN (:company)')
                ->setParameter('company', $companies);
        }


        if ($user) {
            $qb->andWhere('user.id IN (:user)')
                ->setParameter('user', $user);
        }

        return  $qb->getQuery()->getResult();
    }

    public function getNumberOfUsersByCompanyId()
    {
        $companyId = $this->filterService->getAllCompaniesForUser();
        $qb = $this->createQueryBuilder('userClient')
            ->select('company.id, COUNT(company.id) as nbUsers')
            ->join('userClient.company', 'company')
            ->addGroupBy('company.id');

        if ($companyId) {
            $qb->where('company.id IN (:id)')
                ->setParameter('id', $companyId);
        }

        $res = $qb->getQuery()->getResult();


        $resData = array();

        foreach ($res as $value) {
            $resData[$value['id']] = $value['nbUsers'];
        }

        return $resData;
    }
}