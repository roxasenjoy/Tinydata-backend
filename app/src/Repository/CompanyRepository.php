<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Matrix;
use App\Entity\Skill;
use App\Entity\User;
use App\Entity\UserClient;
use App\Service\DateService;
use App\Service\GlobalFilterService;
use App\Service\UserService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Expr\Value;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Company|null find($id, $lockMode = null, $lockVersion = null)
 * @method Company|null findOneBy(array $criteria, array $orderBy = null)
 * @method Company[]    findAll()
 * @method Company[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyRepository extends ServiceEntityRepository
{
    private $filterService;

    CONST maxDepth = 10;

    /**
     * @var DateService $dateService
     */
    private $dateService;
    private $userService;

    public function __construct(ManagerRegistry $registry, GlobalFilterService $filterService, DateService $dateService, UserService $userService)
    {
        parent::__construct($registry, Company::class);

        $this->filterService = $filterService;
        $this->dateService = $dateService;
        $this->userService = $userService;
    }

    public function getOpenAccounts($args){
            
        $companies = $this->filterService->getAllCompaniesForUser();

        // Second requête pour s'occuper de compter le nombre de user par formation par entreprise
        $secondQb = $this->getEntityManager()->getRepository(UserClient::class)->createQueryBuilder('userClient')
                    ->select('COUNT(userClient.id)') // user.email -> A RECUPERER
                    // ->join('userClient.user', 'user') // RAJOUTE 
                    ->andWhere('userClient.inscriptionDate > :beginDate')
                    ->andWhere('userClient.inscriptionDate < :endDate')
                    ->andWhere('userClient.company = company.id ')
                    ->getQuery()->getDQL();
                    
        // Requête principale : S'occupe de gérer toutes les entreprises parent + select ce qu'on souhaite
        $qb =   $this->createQueryBuilder('company')
                ->select(
                    '
                    company.name as name, 
                    company.id as companyId, 
                    parent1.id as parent1Id,
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
                    parent10.name as parent10Name,
                    company.companyDepth as depth,
                    matrix.id as formationId,
                    matrix.name as formationName
                    '
                    )

                ->setParameter('beginDate', $args['beginDate'])
                ->setParameter('endDate', $args['endDate'])
                
                ->leftJoin('company.matrices', 'matrix')
                // Second select
                ->addSelect('(' . $secondQb . ') as value');
                
                $qb 
                ->addOrderBy('company.companyDepth')
                ->addOrderBy('matrix.id')
                ->addOrderBy('value', 'DESC')
                ;

                // Filtres globaux
                if($companies){
                    $qb->where('company.id IN (:company)')
                    ->setParameter('company', $companies);
                }
                if($args['matrixFilter']){
                    $qb->andWhere('matrix.id IN (:matrixFilter)')
                        ->setParameter(':matrixFilter', $args['matrixFilter']);
                }
                
        $this->filterService->setParentsConditions($args['organisationsFilter'], $qb); // Filtres les entreprises sélectionnées par l'utilisateur

        
        return $qb->getQuery()->getResult();

    }

    public function getCompanyAndChilds($companyId){
        
        $qb = $this->createQueryBuilder('company')
        ->where('company.id = :id')
        // ->andWhere('company.deletedAt IS NULL')
        ->setParameter('id', $companyId);

        for($i=1; $i <=10; $i++){
            $qb->orWhere('company.parent'.$i.' = :id');
        }
        

        return $qb->getQuery()->getResult();
    }


    public function findCompaniesTinydata($researchBar){
        $filterCompany = $this->filterService->getAllCompaniesForUser();

        $qb = $this->createQueryBuilder('company')
            ->select('company.id, company.name')
            ->andWhere('company.deletedAt IS NULL') 
            ->andWhere('company.name LIKE :searchTerm')
            ->setParameter('searchTerm',  '%'.$researchBar.'%');

        if($filterCompany)
        {
            $qb->andWhere('company.id in (:company_id)')
            ->setParameter('company_id', $filterCompany);
        }

        return $qb->getQuery()->getResult();
    }

    public function getChildsOfParentId($id){

        $qb = $this->createQueryBuilder('company')
            ->select('company.id')
            ->orWhere('company.id = :id')
            ->orWhere('company.parent1 = :id')
            ->setParameter(':id', $id);


        return $qb->getQuery()->getResult();


    }

    public function getParentCompany(){

        $company = $this->filterService->getAllCompaniesForUser();

        $qb = $this->createQueryBuilder('company')
                ->select('company.id, company.name')
                ->andWhere('company.parent1 IS NULL')
                ->andWhere('company.deletedAt IS NULL')
                ->orderBy('company.name');

        if($company){
            $qb->andWhere('company.id IN (:company_id)')
                ->setParameter('company_id', $company);
        }

    

        return $qb->getQuery()->getResult();
    }

    /**
     * Permet de déterminer les entreprises de l'utilisateur
     */
    public function getChildsCompanies(){

        $user = $this->userService->getUser();

        $qb = $this->createQueryBuilder('company')
                ->select("
                company.id as id, 
                company.name, 
                company.companyDepth as depth,
                company.id as companyId, 
                parent1.id as parent1Id,
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
                parent10.name as parent10Name")
                ->andWhere('company.deletedAt IS NULL')
                ->orderBy('company.companyDepth, company.name');

        for($i=1; $i<=self::maxDepth; $i++){
            $qb->leftJoin('company.parent' . $i . '' ,'parent' . $i);
        }

        $depth = $user->getCompany()->getCompanyDepth();
        if(!in_array(User::ROLE_ADMIN, $user->getRoles())){ 
            $qb->where('company.id = :companyId')
            ->orWhere('company.parent'.$depth.' = :companyId')
            ->setParameter('companyId', $user->getCompany()->getId());
        }

        return $qb->getQuery()->getResult();

    }


    public function getCompanies(){
        $filterCompany = $this->filterService->getAllCompaniesForUser();

        $qb = $this->createQueryBuilder('company')
            ->select('company.id, company.name')
            ->andWhere('company.deletedAt IS NULL')
            ->orderBy('company.name');

        if($filterCompany)
        {
            $qb->andWhere('company.id in (:company_id)')
            ->setParameter('company_id', $filterCompany);
        }

        return $qb->getQuery()->getResult();
    }


    public function getCompanyName($idCompany){
        $filterCompany = $this->filterService->getAllCompaniesForUser();

        $qb = $this->createQueryBuilder('company')
            ->select('company.name')
            ->andWhere('company.deletedAt IS NULL')
            ->andWhere('company.id = :idCompany')
            ->setParameter('idCompany', $idCompany);

        if($filterCompany)
        {
            $qb->andWhere('company.id in (:company_id)')
            ->setParameter('company_id', $filterCompany);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     *
     * Récupère toutes les formations de l'entreprise (En fonction de l'ID utilisateur)
     *
     * @param $idCompany
     * @param bool $type
     * @param array $matrixFilter
     * @return int|mixed|string
     */
    public function getFormationsCompanies($idCompany, $onlyId = false){
        $qb= $this->createQueryBuilder('company');

            if($onlyId === true){
                $qb ->select('matrix.id');
            } else {
                $qb ->select('matrix.name, matrix.id');
            }

            $qb ->innerJoin('company.matrices', 'matrix')
                ->andWhere('company.id = (:companyId)')
                ->setParameter('companyId', $idCompany);

                

        return  $qb->getQuery()->getResult();
    }

    /**
     * Obtenir toutes les formations se trouvant dans l'entreprise parent
     */
    public function getAllFormationsFromParentCompany($idCompany, $onlyId = false){
        $qb= $this->createQueryBuilder('company');

        if($onlyId === true){
            $qb ->select('matrix.id');
        } else {
            $qb ->select('matrix.name, matrix.id');
        }
        
        $qb ->innerJoin('company.matrices', 'matrix')
            ->andWhere('company.id IN (:companyId)')
            ->setParameter('companyId', $idCompany);

        return  $qb->getQuery()->getResult();
    }

    /**
     *
     * Récupère l'id de l'entreprise en fonction de son nom
     *
     * @param $name
     * @return int|mixed|string
     */
    public function getIdCompany($name){
        $qb = $this->createQueryBuilder('company')
            ->select('company.id')
            ->andWhere('company.name = :companyName')
            ->andWhere('company.deletedAt IS NULL')
            ->setParameter('companyName', $name);

        return $qb->getQuery()->getResult();
    }

    public function getNameCompany($id){
        $qb = $this->createQueryBuilder('company')
            ->select('company.name')
            ->andWhere('company.id = :id')
            ->andWhere('company.deletedAt IS NULL')
            ->setParameter('id', $id);

        return $qb->getQuery()->getResult();
    }

    /*********************************************************************************
     * Fonction rajouter après le changement de base de données sur les entreprises
     ********************************************************************************/

     /**
      * Obtiens toutes les entreprises pour l'utilisateur connecté
      */
    public function getCompaniesForUser($user){
        $qb =   $this->createQueryBuilder('company')
                ->orderBy('company.companyDepth', 'ASC');
            
        $depth = $user->getCompany()->getCompanyDepth();
        if(!in_array(User::ROLE_ADMIN, $user->getRoles())){ 
            $qb->where('company.id = :companyId')
            ->orWhere('company.parent'.$depth.' = :companyId')
            ->setParameter('companyId', $user->getCompany()->getId());
        }
        return $qb->getQuery()->getResult();
    }

     /**
     * Obtiens toutes les entreprises enfants des entreprises associés à l'utilisateur
     * @param $allowedCompanies - Liste des entreprises dont l'utilisateur a accès
     */
    public function getSpecifiedChildsCompaniesForUser($allowedCompanies){

        $qb = $this ->createQueryBuilder('company')
                    // ->andWhere('company.deletedAt IS NULL')
                    ->andWhere('company.id IN (:companyId)')
                    ->orderBy('company.parent1', 'ASC');

        for ($depth = 1; $depth <= 10; $depth++) {
            $qb ->orWhere('company.parent'.$depth.' IN (:companyId)');
        }
        
        $qb->setParameter('companyId', $allowedCompanies);
 
        return $qb->getQuery()->getResult();
    }

    /**
     * Obtiens toutes les entreprises enfants de l'entreprise de l'utilisateur connecté
     */
    public function getChildsCompaniesForUser($user){
        $depth = $user->getCompany()->getCompanyDepth();

        $qb = $this ->createQueryBuilder('company')
                    // ->andWhere('company.deletedAt IS NULL')
                    ->orderBy('company.parent1', 'ASC');

        if(in_array(User::ROLE_ADMIN, $user->getRoles())){
            $qb->andWhere('company.companyDepth > 1');
        }
        else
        {
            $qb->andWhere('company.parent'.$depth.' = :companyId')
            ->setParameter('companyId', $user->getCompany()->getId());
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Obtiens l'entreprises mère de l'entreprise enfant de l'utilisateur connecté
     */
    public function getMothersCompaniesForUser($user){
        $qb = $this->createQueryBuilder('company')
        ->orderBy('company.name', 'ASC');

        if(in_array(User::ROLE_ADMIN, $user->getRoles())){
            $qb->where('company.companyDepth = 1');
            return $qb->getQuery()->getResult();

        }
        else{
            return [$user->getMotherCompany()];
        }


    }
}