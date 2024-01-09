<?php

namespace App\Repository;

use App\Entity\Contract;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Contract|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contract|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contract[]    findAll()
 * @method Contract[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContractRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contract::class);
    }

    public function getContract($idCompnay){
        return $this->createQueryBuilder('contract')
            ->select('contract.id, contract.dateFrom, contract.dateTo, contractType.name')
            ->join('contract.contractType', 'contractType')
            ->leftJoin('contract.companies', 'company')
            ->where('company.id = :id')
            ->setParameter(':id', $idCompnay)
            ->getQuery()
            ->getResult();

    }

}
