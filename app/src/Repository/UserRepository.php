<?php

namespace App\Repository;

use App\Entity\User;
use App\Service\GlobalFilterService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    private $filterService;

    public function __construct(ManagerRegistry $registry, GlobalFilterService $filterService)
    {
        parent::__construct($registry, User::class);
        $this->filterService = $filterService;
    }


    public function findNameUsersTinydata($researchBar){

        $filterCompany = $this->filterService->getAllCompaniesForUser();

        $qb = $this->createQueryBuilder('user')
            ->select('user.id, user.firstName, user.lastName')
            ->join('user.userClient', 'userClient')
            ->join('userClient.company', "company");

        //Permet de séparer le prénom et le nom
        $parts = explode(" ", $researchBar);

        /**
         * La recherche contient un prénom et un nom
         */
        if(count($parts) === 2){
            $firstName = $parts[0];
            $lastName = $parts[1];

            $qb ->andWhere('user.firstName LIKE :firstName')
                ->andWhere('user.lastName LIKE :lastName')
                ->setParameter('firstName',  '%'.$firstName.'%')
                ->setParameter('lastName',  '%'.$lastName.'%');
        } else {
            $qb ->andWhere('user.firstName LIKE :searchTerm')
                ->orWhere('user.lastName LIKE :searchTerm')
                ->setParameter('searchTerm',  '%'.$researchBar.'%');
        }

        if($filterCompany)
        {
            $qb->andWhere('company.id in (:company_id)')
            ->setParameter('company_id', $filterCompany);
        }

        return $qb ->getQuery()->getResult();
    }

    public function findEmailUsersTinydata($researchBar){

        $filterCompany = $this->filterService->getAllCompaniesForUser();

        $qb = $this->createQueryBuilder('user')
            ->select('user.id, user.email')
            ->join('user.userClient', 'userClient')
            ->join('userClient.company', "company")
            ->andWhere('user.email LIKE :searchTerm')
            ->setParameter('searchTerm',  '%'.$researchBar.'%');

            if($filterCompany)
            {
                $qb->andWhere('company.id in (:company_id)')
                ->setParameter('company_id', $filterCompany);
            }

        return $qb->getQuery()->getResult();
    }

    public function getAccounts($idAccount = null, $companyId = null, $searchText = null){
        //On filtre pas defaut sur toutes les entreprises
        if(!$companyId){
            $companyId = $this->filterService->getAllCompaniesForUser();
        }

        $qb = $this->createQueryBuilder('user')
            ->select('user.id, user.firstName, user.lastName, user.email, company.id as company_id')
            ->innerJoin('user.userClient', 'userClient')
            ->join('userClient.company', 'company')
            // ->join('user.permissions', 'permissions', 'WITH', 'permissions.id = '.GlobalFilterService::TINYDATA_PERMISSION)
            ->orderBy('user.id', 'DESC');
            
            if($companyId){
                $qb->where('userClient.company in (:company_id)')
                ->setParameter('company_id', $companyId);
            }

            //Filtre sur un id user précis
            if($idAccount){
               $qb ->andWhere('user.id LIKE :idUser')
                ->setParameter('idUser',  $idAccount);
            }

            //Filtre sur le nom/prénom/mail des users
            if($searchText){
                $qb ->andWhere('user.firstName LIKE :searchText OR user.lastName LIKE :searchText OR user.email LIKE :searchText')
                 ->setParameter('searchText',  "%".$searchText."%");
            }

            return $qb->getQuery()->getResult();
    }


    public function researchUsers($value){
        //On filtre pas defaut sur toutes les entreprises
        
        $companyId = $this->filterService->getAllCompaniesForUser();
       
        $qb = $this->createQueryBuilder('user')
            ->innerJoin('user.userClient', 'userClient')
            ->join('userClient.company', 'company')
            ->orderBy('user.lastName')
            ->orderBy('user.firstName');
            if($companyId){
                $qb->where('userClient.company in (:company_id)')
                ->setParameter('company_id', $companyId);
            }
        
        //Filtre sur le nom/prénom/mail des users
        if($value){
            $qb ->andWhere('user.firstName LIKE :searchText OR user.lastName LIKE :searchText')
                ->setParameter('searchText',  "%".$value."%");
        }

        return $qb->getQuery()->getResult();
       
    }

    public function getUsers($idAccount = null, $companyId = null, $searchText = null){
        //On filtre pas defaut sur toutes les entreprises
        if(!$companyId){
            $companyId = $this->filterService->getAllCompaniesForUser();
        }

        $qb = $this->createQueryBuilder('user')
            ->innerJoin('user.userClient', 'userClient')
            ->join('userClient.company', 'company')
            ->orderBy('user.lastName')
            ->orderBy('user.firstName');
            
        if($companyId){
            $qb->where('userClient.company in (:company_id)')
            ->setParameter('company_id', $companyId);
        }

        //Filtre sur un id user précis
        if($idAccount){
            $qb ->andWhere('user.id LIKE :idUser')
            ->setParameter('idUser',  $idAccount);
        }

        //Filtre sur le nom/prénom/mail des users
        if($searchText){
            $qb ->andWhere('user.firstName LIKE :searchText OR user.lastName LIKE :searchText OR user.email LIKE :searchText')
                ->setParameter('searchText',  "%".$searchText."%");
        }

        return $qb->getQuery()->getResult();
    }

    public function setCompany($id, $idUser){
        return $this->createQueryBuilder('user') // Table de base
            ->update('App\Entity\User', 'user')
            ->innerJoin('user.userClient', 'userClient')
            ->innerJoin('userClient.company', 'company')
            ->where('user.id LIKE :idUser')
            ->setParameter('idUser', $idUser)
            ->set('userClient.company', ':id')
            ->setParameter('id', $id)

            ->getQuery()
            ->getResult();
    }

    public function getUserId($email){
        return $this->createQueryBuilder('user')
            ->select('user.id')
            ->where('user.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getResult();
    }

    public function getUserClientId($id){
        return $this->createQueryBuilder('user')
            ->select('userClient.id')
            ->innerJoin('user.userClient', 'userClient')
            ->where('user.id = :idUser')
            ->setParameter('idUser', $id)
            ->getQuery()
            ->getResult();
    }

    public function getUserWithUserClientId($userclientid){
        return $this->createQueryBuilder('user')
            ->innerJoin('user.userClient', 'userClient')
            ->where('userClient.id = :idUserClient')
            ->setParameter('idUserClient', $userclientid)
            ->getQuery()
            ->getResult();
    }

    public function getUserWithUserClientIdBis($userclientid){
        return $this->createQueryBuilder('user')
            ->innerJoin('user.userClient', 'userClient')
            ->where('userClient.id IN (:idUserClient)')
            ->setParameter('idUserClient', $userclientid)
            ->getQuery()
            ->getResult();
    }

    public function getUserCompanyID($idUser){
        return $this->createQueryBuilder('user') // Table de base
            ->select('company.id')
            ->innerJoin('user.userClient', 'userClient')
            ->innerJoin('userClient.company', 'company')
            ->where('user.id = :id')
            ->setParameter('id', $idUser)

            ->getQuery()
            ->getResult();
    }

    public function getNumberUserByMatrix($args){

        if(!$args['organisationsFilter']){
            $args['organisationsFilter'] = $this->filterService->getAllCompaniesForUser();
        }

        $qb = $this->createQueryBuilder('user')
        ->join("user.userClient", "userClient")
        ->join("userClient.company", "company")
        ->join("company.matrices", "matrix")
        ->select("count(user.id) as total, matrix.id as id, matrix.name")
        ->groupBy("matrix.id");

        if(!$args['filterFormationSelected']){
            $args['filterFormationSelected'] = '';
        }

        if($args['filterFormationSelected']){
            $qb->andWhere("matrix.id = :filterFormationSelected")
            ->setParameter('filterFormationSelected', $args['filterFormationSelected']);
        }

        if($args['organisationsFilter']){
            $qb->andWhere("userClient.company IN (:companies)")
            ->setParameter("companies", $args['organisationsFilter']);
        }

        if($args['idUser']){
            $qb->andWhere("user.id = :userId")
            ->setParameter("userId", $args['idUser']);
        }

        return $qb->getQuery()->getResult();
    }

    public function getAllMatrixes($companies, $matrixFilter, $userId){
        
        $companyFilter = $this->filterService->getAllCompaniesForUser();

        $qb = $this->createQueryBuilder('user')
            ->select("matrix.id, matrix.name")
            ->distinct(true)
            ->join("user.userClient", "userClient")
            ->join("userClient.company", "company")
            ->join("company.matrices", "matrix")
            ->andWhere('company.deletedAt IS NULL')
            ->orderBy('matrix.name');

        if($userId){
            $qb ->andWhere('user.id = :userId')
                ->setParameter(':userId', $userId);
        }

        if($companies){
            $qb->andWhere('company.id IN (:companies)')
                        ->setParameter(':companies', $companies);
        }

        if($matrixFilter){
            $qb ->andWhere('matrix.id IN (:matrixFilter)')
                ->setParameter(':matrixFilter', $matrixFilter);
        }

        if($companyFilter){            
            $qb->andWhere("company.id IN (:companyList)")
            ->setParameter('companyList', $companyFilter);
        }
        
        return $qb->getQuery()->getResult();

    }
}
