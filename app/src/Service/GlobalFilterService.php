<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\Matrix;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class GlobalFilterService
 *
 * @package App\Service
 */
class GlobalFilterService
{
    //id de la permission tinydata dans la base tinycoaching autorisant la connexion à tinydata
    const TINYDATA_PERMISSION = 3;

    const DEPTH_MATRIX = "matrix";
    const DEPTH_DOMAIN = "DEPTH_DOMAIN";
    const DEPTH_SKILL = "SKILL";
    const DEPTH_THEME = "THEME";

    const maxDepth = 10;

    private $em;
    private $userService;
    private $securizerService;

    public function __construct(ManagerRegistry $mr, UserService $userService, SecurizerService $securizerService)
    {
        $this->em = $mr->getManager("tinycoaching");
        $this->userService = $userService;
        $this->securizerService = $securizerService;
    }

    /**
     * @return object - return the company in which the user is
     * 
     * Attention !!!!! - Il vaut mieux utiliser la fonction $this->getAllCompaniesForUser() qui gère les organisations et les sous organisations
     * en fonction du personas de l'utilisateur
     */
    public function getUserCompany($needEntity = false)
    {
        $user = $this->userService->getUser();
        $company = $user->getCompany();
        if ($needEntity) {
            return $company;
        }

        return $company->getId();
    }

    /**
     * @return array - return all the companies linked to the user : 
     * return the user's company, his childs and his parent if exist.
     * 
     *   ROLE_SU & ROLE_ADMIN : qui ont accès à tout
     *   ROLE_SU_CLIENT : qui a accès à son groupe racine + tous ses enfants
     *   ROLE_USER_ADMIN & ROLE_TINYDATA : qui ont accès à rien, hormis ce qui est spécifié dans la table user_company (et leurs enfants)
     */
    public function getAllCompaniesForUser($needEntities = false)
    {

        $user = $this->userService->getUser();

        if ($this->userService->userIsSuperAdmin(false)) { // Admin Tinycoaching
            return null;
        }

        switch ($user->getRoles()[0]) {
            case 'ROLE_SU_USER_ADMIN': // Super admin client
                // Récupère toutes les entreprises 
                $companies = $this->em->getRepository(Company::class)->getCompanyAndChilds($this->getUserCompany(false));
                break;

            case 'ROLE_SU_CLIENT':
                // On récupère tous les enfants de l'entreprise de l'utilisateur
                $companies = $this->em->getRepository(Company::class)->getChildsCompaniesForUser($user);
                // On rajoute l'entreprise de l'utilisateur lui-même
                array_push($companies, $user->getCompany());
                break;

            case 'ROLE_USER_ADMIN':
            case 'ROLE_TINYDATA':

                // Récupération de toutes les entreprises spécifié dans la table user_company
                $allowedCompanies = [];
                foreach ($user->getCompaniesInScope() as $company) {
                    array_push($allowedCompanies, $company->getId());
                }

                // RESOLUTION BUG URGENT - fondouest-normandie50@fondouest.com
                // Si l'utilisateur n'a pas le droit d'accéder à des données, on return une entreprise qui n'existera jamais -1
                if ($allowedCompanies === []) {
                    return [-1];
                }

                $companies = $this->em->getRepository(Company::class)->getSpecifiedChildsCompaniesForUser($allowedCompanies, $user);

                break;

            default:
                return [$user->getCompany()->getId()];
        }

        if ($needEntities === false) {
            $ids = array();
            foreach ($companies as $company) {
                array_push($ids, $company->getId());
            }

            return $ids;
        }

        return $companies;
    }


    /**
     * Permet d'obtenir tous les rôles disponibles (mère + filles) en fonction de l'id de l'entreprise
     */
    public function getAllRolesByCompanyId($roleId = null, $companyId = null, $searchText = null)
    {

        // Obtenir toutes les entreprises de l'utilisateur en fonction de l'id de l'entreprise
        $companies = $this->em->getRepository(Company::class)->getCompanyAndChilds($companyId);

        // Récupérer les ID de toutes les entreprises
        $ids = array();
        foreach ($companies as $company) {
            array_push($ids, $company->getId());
        }

        // On récupère les rôles sur la base de données de Tinydata
        $roles = $this->em->getRepository(Roles::class)->getRoles(null, $ids, null);

        return $roles;
    }

    /**
     * @return array - return all matrixes accessible for a company
     */
    public function getAllMatrixes($needEntities = false)
    {
        if ($this->userService->userIsSuperAdmin(false)) {
            return null;
        }
        $matrixes = $this->em->getRepository(Matrix::class)->findMatrixesForUser();

        if ($needEntities === false) {
            $ids = array();
            foreach ($matrixes as $matrix) {
                array_push($ids, $matrix->getId());
            }

            return $ids;
        }

        return $matrixes;
    }

    /**
     * @return array - return all roles accessible for a company
     */
    public function getAllRoles($needEntities = false)
    {
        $companies = $this->getAllCompaniesForUser();
        $roles = $this->em->getRepository(Roles::class)->getRoles(null, $companies, null);

        if ($needEntities === false) {
            $ids = array();
            foreach ($roles as $role) {
                array_push($ids, $role['id']);
            }

            return $ids;
        }

        return $roles;
    }


    /**************************************************************************************************************************
     * 
     *                                  Gestion des filtres présents dans les requêtes DOCTRINE
     * 
     **************************************************************************************************************************/


    /**
     * @return QueryBuilder - set formation filter under graphic
     */
    public function setFormationSelectedFilterInRepository($args, $qb){
        
        if($args['filterSelected'] || $args['filterSelected'] === "0"){
            $qb ->andWhere('matrix.id = :filterSelected')
                ->setParameter(':filterSelected', $args['filterSelected']);
        }

        return $qb;
    }

    /**
     * @return QueryBuilder - set allowed companies to filter
     */
    public function setAllCompaniesForUserFilter($qb){

        $userCompanies = $this->getAllCompaniesForUser();
    
        if ($userCompanies) {
            $qb->andWhere('company.id in (:organisations)')
                ->setParameter('organisations', $userCompanies);
        }

        return $qb;

    }

    /**
     * @return QueryBuilder - set 'FORMATIONS' filter to current query builder
     */
    public function setFormationsFilterInRepository($args, $qb)
    {

        if ($args['matrixFilter']) {
            $qb->andWhere('matrix.id in (:matrixId)')
                ->setParameter('matrixId', $args['matrixFilter']);
        }

        if ($args['domainFilter']) {
            $qb->andWhere('domain.id in (:domainId)')
                ->setParameter('domainId', $args['domainFilter']);
        }

        if ($args['skillFilter']) {
            $qb->andWhere('skill.id in (:skillId)')
                ->setParameter('skillId', $args['skillFilter']);
        }

        if ($args['themeFilter']) {
            $qb->andWhere('theme.id in (:themeId)')
                ->setParameter('themeId', $args['themeFilter']);
        }

        return $qb;
    }

    /**
     * @return QueryBuilder - set 'FORMATIONS' filter to current query builder
     */
    public function setOrganisationsFilterInRepository($args, $qb)
    {

        if ($args['organisationsFilter']) {
            $qb->andWhere('company.id in (:organisations)')
                ->setParameter('organisations', $args['organisationsFilter']);
        }

        return $qb;
    }

    /**
     * @return QueryBuilder - set 'UserId' filter to current query builder
     */
    public function setUserFilterInRepository($args, $qb)
    {

        if($args['idUser']){
            $qb->andWhere("user.id = :userId")
            ->setParameter("userId", $args['idUser']);
        }

        return $qb;
    }

    /*
     * @return QueryBuilder - set 'BEGIN DATE & END DATE' filter to current query builder
     */
    public function setDateFilterInRepository($args, $qb)
    {

        if ($args['beginDate']) {
            $qb->andWhere('us.date >= :beginDate')
                ->setParameter(':beginDate', $args['beginDate']);
        }

        if ($args['endDate']) {
            $qb->andWhere('us.date <= :endDate')
                ->setParameter(':endDate', $args['endDate']);
        }

        return $qb;
    }

    /**
     * @return QueryBuilder - set 'Parent' filter to current query builder
     */
    public function setParentsConditions($organisationsFilter, $qb)
    {

        for ($depthJoin = 1; $depthJoin <= self::maxDepth; $depthJoin++) {
            $qb->leftJoin('company.parent' . $depthJoin, 'parent' . $depthJoin);
        }

        if ($organisationsFilter) {
            for ($depthWhere = 1; $depthWhere <= self::maxDepth; $depthWhere++) {
                $qb->orWhere('parent' . $depthWhere . '.id IN (:organisationsFilter)');
            }
            $qb->setParameter(':organisationsFilter', $organisationsFilter);
        }


        return $qb;
    }
}
