<?php

namespace App\Repository;

use App\Entity\AnalyticsLeitner;
use App\Service\GlobalFilterService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AnalyticsLeitner|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnalyticsLeitner|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnalyticsLeitner[]    findAll()
 * @method AnalyticsLeitner[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnalyticsLeitnerRepository extends ServiceEntityRepository
{

    private $filterService;

    public function __construct(ManagerRegistry $registry, GlobalFilterService $filterService)
    {
        parent::__construct($registry, AnalyticsLeitner::class);
        $this->filterService = $filterService;
    }

    public function getAnalyticsLeitner($args, $specificGraphData)
    {
        $qb = $this->createQueryBuilder('al')
            ->select(
                '  al.id as alId, 
                        al.current_box,
                        al.nb_memory_done,
                        al.nb_memory_failed,
                        al.memory_progression,
                        userClient.id as userClientId,
                        user.id as userId,
                        user.email as userEmail,
                        user.firstName,
                        user.lastName,
                        parent1.name as parent1Name,
                        company.name as companyName,
                        company.id as companyId,
                        acquis.title as acquisName,
                        acquis.id as acquisId,
                        theme.id as themeId,
                        skill.id as skillId,
                        domain.id as domainId,
                        matrix.name as matrixName,
                        matrix.id as matrixId
                        '
            )
            ->innerJoin('al.user_client', 'userClient')
            ->innerJoin('userClient.user', 'user')
            ->innerJoin('userClient.company', 'company')
            ->innerJoin("al.acquis", "acquis")
            ->innerJoin("al.theme", "theme")
            ->innerJoin('al.skill', 'skill')
            ->innerJoin("al.domain", "domain")
            ->innerJoin('al.matrix', 'matrix')

            ->orderBy('acquis.id', 'ASC');

        $this->filterService->setOrganisationsFilterInRepository($args, $qb);
        $this->filterService->setFormationSelectedFilterInRepository($specificGraphData, $qb);
        $this->filterService->setAllCompaniesForUserFilter($qb);
        $this->filterService->setFormationsFilterInRepository($args, $qb);
        $this->filterService->setUserFilterInRepository($args, $qb);
        $this->filterService->setParentsConditions($args['organisationsFilter'], $qb);

        return $qb->getQuery()->getResult();
    }


    public function getMatrixWithRappelsMemo($organisationsFilter, $matrixFilter, $userId, $beginDate, $endDate)
    {
        $qb = $this->createQueryBuilder('al')
            ->select('matrix.id as matrixID', 'matrix.name')
            ->innerJoin('al.user_client', 'userClient')
            ->innerJoin('userClient.user', 'user')
            ->innerJoin('userClient.company', 'company')
            ->innerJoin("al.acquis", "acquis")
            ->innerJoin("al.theme", "theme")
            ->innerJoin('al.skill', 'skill')
            ->innerJoin("al.domain", "domain")
            ->innerJoin('al.matrix', 'matrix')

            ->groupBy('matrix.id')
            ->orderBy('matrix.id');

        if ($matrixFilter) {
            $qb->andWhere('matrix.id IN (:matrixFilter)')
                ->setParameter(':matrixFilter', $matrixFilter);
        }

        if ($userId) {
            $qb->andWhere('user.id IN (:userFilter)')
                ->setParameter(':userFilter', $userId);
        }

        if ($organisationsFilter) {
            $qb->andWhere("company.id IN (:companyList)")
                ->setParameter('companyList', $organisationsFilter);
        }

        $this->filterService->setAllCompaniesForUserFilter($qb);

        return $qb->getQuery()->getResult();
    }
}
