<?php


namespace App\Resolver;

use App\Entity\Company;
use App\Entity\Contract;
use App\Entity\Profile;
use App\Entity\User;
use App\Entity\UserClient;
use App\Repository\CompanyRepository;
use App\Service\GlobalFilterService;
use App\Service\UserService;

class CompanyResolver extends AbstractResolver
{
    /**
     * @var CompanyRepository
     */
    private $companyRepository;
    private $filterService;
    private $userService;

    /**
     * CompanyResolver constructor.
     * @param CompanyRepository $companyRepository
     */
    public function __construct(
        CompanyRepository $companyRepository,
        GlobalFilterService $filterService,
        UserService $userService
    ) {
        $this->filterService = $filterService;
        $this->companyRepository = $companyRepository;
        $this->userService = $userService;
    }

    // CODE A GARDER MEP
    public function getCompanies($joinCompany = false)
    {
        $user = $this->userService->getUser();
        $depth = 1;
        $tree = $this->companyRepository->getParentCompany();
        $childs = $this->companyRepository->getChildsCompanies();

        if (empty($tree)) {
            $tree = [['name' => $user->getCompany()->getName(), 'childs' => [], 'id' => $user->getCompany()->getId()]];
            $depth = $childs[0]['depth']; // Peut être égal de 2 à 10;
        }

        $this->addChilds($childs, $tree, $depth); // Validé - Récupère toutes la granularité en forme de tree

        if (in_array(User::ROLE_TINYDATA, $user->getRoles()) || in_array(User::ROLE_USER_ADMIN, $user->getRoles())) {
            $index = [];
            $treeReformed = [];
            $userCompanies = $this->filterService->getAllCompaniesForUser(); // Validé

            foreach($userCompanies as $company){
                if(!in_array($company, $index)){
                    $companyToFind = $this->addCompanies($tree[0]['childs'], $company);
                    array_push($treeReformed, $companyToFind);
                    $this->addCompanyToIndex($companyToFind, $index);
                }
            }

            $tree = $treeReformed;
            

        } else {
            foreach($tree as $key => $parent){
                $tree[$key] = $parent['childs'][0];
            }
        }

        return $tree;
    }

    private function addCompanyToIndex($array, &$ids){
        if (isset($array['id']) && !in_array($array['id'], $ids)) {
            $ids[] = $array['id'];
        }
        if (isset($array['childs']) && is_array($array['childs'])) {
            foreach ($array['childs'] as $child) {
                $this->addCompanyToIndex($child, $ids);
            }
        }
    }

    /**
     * Récursive function
     * @param childs -> Tous les enfants en se basant sur la première profondeur de l'utilisateur
     * @param id -> Id de toutes les entreprises récupérées
     */
    private function addCompanies($childs, $id)
    {

        foreach($childs as $child){

            // Si l'id de l'enfant est le même que l'id return, c'est à dire que nous sommes à la dernière granularité
            if($child['id'] === $id){
                return $child;
            } else {
                // Si il possède la key : CHILDS, on rentre dans la condition
                if(array_key_exists('childs', $child)){
                    $companyToFind = $this->addCompanies($child['childs'], $id);
                    if($companyToFind !== false){
                        return $companyToFind;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param childs -> Tous les enfants en se basant sur la première profondeur de l'utilisateur
     * @param trees -> L'arbre des entreprises
     * @param depth -> Profondeur des entreprises
     */
    private function addChilds($childs, &$trees, $depth)
    {

        // Boucle pour chaque tree dans le tableau `$parents`
        foreach ($trees as &$tree) {
            // Clé pour accéder à l'identifiant du parent, en fonction de la profondeur
            $parentIdKey = $depth !== 1 ? "parent" . ($depth - 1) . "Id" : 'id';

            // Filtrage des enfants à ajouter en fonction de leur profondeur et de l'identifiant du tree
            $childsToAdd = array_filter($childs, function ($child) use ($tree, $parentIdKey, $depth) {
                return $child['depth'] === $depth && $child[$parentIdKey] === $tree['id'];
            });

            // Boucle pour chaque enfant à ajouter
            foreach ($childsToAdd as $childToAdd) {
                // Si le tree n'a pas encore de tableau d'enfants, on le crée
                if (!array_key_exists('childs', $tree)) {
                    $tree['childs'] = [];
                }

                // Vérification que l'enfant n'existe pas déjà dans les enfants du tree, avant de l'ajouter
                if (!in_array($childToAdd, $tree['childs'])) {
                    array_push($tree['childs'], $childToAdd);
                }
            }

            // Appel récursif de la fonction pour ajouter les enfants des enfants (et ainsi de suite)
            if ($depth < 10 && array_key_exists('childs', $tree) && !is_null($tree['childs'])) {
                $this->addChilds($childs, $tree['childs'], $depth + 1);
            }
        }
    }


    /**
     * Récupérer toutes les entreprises de l'utilisateur connecté
     * 
     * Return une liste d'entreprise
     */
    public function getCompaniesNames($idCompany)
    {

        $allCompanies = $this->filterService->getAllCompaniesForUser();

        // Si l'utilisateur posséde qu'une seule entreprise
        if ($allCompanies === null) {
            $result = true;
        } else {
            if (gettype($allCompanies) !== "integer") {

                // On vérifie que la page actuelle fait parti de la liste des entreprises de l'utilisateur
                if (in_array($idCompany, $allCompanies)) {
                    $result = true;
                } else {
                    $result = false;
                }
            } else {
                if ($allCompanies === $idCompany) {
                    $result = true;
                } else {
                    $result = false;
                }
            }
        }


        $isInCompany = array("inCompany" => $result);

        return $isInCompany;
    }


    public function getCompanyNameById($id)
    {
        $name = $this->em->getRepository(Company::class)->getNameCompany($id);
        $companyName = array(
            "id" => $id,
            "name" => $name[0]['name']
        );

        return $companyName;
    }

    /**
     *
     * Récupère et affiche les objectifs
     *
     * @param $name
     * @param $filterObjectifs
     * @return array
     */
    public function getCompanyObjectifs($filterObjectifs)
    {
        $parcours = $this->em->getRepository(Profile::class)->getParcoursCompanies();
        $parcoursArray = [];

        for ($value = 0; $value < count($parcours); $value++) {
            array_push($parcoursArray, $parcours[$value]['jobTitle']);
        }

        $uniqueParcoursArray = array_unique($parcoursArray);

        if (empty($filterObjectifs)) {
            $filterObjectifs = [];
        }

        $getObjectifs = [];
        while (current($uniqueParcoursArray)) {
            $getObjectifs[] = array(
                "id" => key($uniqueParcoursArray),
                "name" => current($uniqueParcoursArray),
                "completed" => in_array(key($uniqueParcoursArray), (array)$filterObjectifs),
                "color" => "primary"
            );
            next($uniqueParcoursArray);
        }

        return $getObjectifs;
    }


    /**
     * Obtenir toutes les informations de l'entreprise
     * Utile pour la page /entreprise/{id}
     */
    public function getCompaniesInformation($idCompany)
    {

        $companyInformation = [];
        $formationsArray = [];
        $parcoursArray = [];

        /*
            L'entreprise est parent
        */
        $formations = $this->em->getRepository(Company::class)->getFormationsCompanies($idCompany); // Peut changer
        
        $date = $this->em->getRepository(Contract::class)->getContract($idCompany);

        $parcours = $this->em->getRepository(UserClient::class)->getParcoursCompanies($idCompany);


        // Eviter les problèmes avec la donnée brute : Nombre total d'apprenants actifs
        $args['organisationsFilter'] = [$idCompany];
        $args['idUser'] = null;
        $usersNumber = $this->em->getRepository(UserClient::class)->getUsersCompanies($args, "false");
        $usersOnBorder = $this->em->getRepository(UserClient::class)->getUsersOnBorder($args);

        

        // Ajouter toutes les formations dans le tableau
        for ($value = 0; $value < count($formations); $value++) {
            array_push($formationsArray, $formations[$value]['name']);
        }
        $formationsArray = array_unique($formationsArray);

        if (empty($formations)) {
            $formationsArray = ['Aucune formation choisie'];
        }
        /**
         * Insère dans un tableau tous les parcours
         *
            self::PROFILE_PRO => ['Multi-parcours'], 1
            self::PROFILE_EMPATHIQUE => ['Empathique'], 2
            self::PROFILE_RELATIONNEL => ['Relationnel'], 3
            self::PROFILE_METHODIQUE => ['Méthodique'], 4
            self::PROFILE_CREATIF => ['Créatif'], 5
            self::PROFILE_VIGILANT => ['Vigilant'], 6
         */
        for ($value = 0; $value < count($parcours); $value++) {

            if ($parcours[$value]['onBoardingProfile'] !== null) {

                if ($parcours[$value]['onBoardingProfile'] == 1) $parcours[$value]['onBoardingProfile'] = 'Multi-parcours';
                else if ($parcours[$value]['onBoardingProfile'] == 2) $parcours[$value]['onBoardingProfile'] = 'Empathique';
                else if ($parcours[$value]['onBoardingProfile'] == 3) $parcours[$value]['onBoardingProfile'] = 'Relationnel';
                else if ($parcours[$value]['onBoardingProfile'] == 4) $parcours[$value]['onBoardingProfile'] = 'Méthodique';
                else if ($parcours[$value]['onBoardingProfile'] == 5) $parcours[$value]['onBoardingProfile'] = 'Créatif';
                else if ($parcours[$value]['onBoardingProfile'] == 6) $parcours[$value]['onBoardingProfile'] = 'Vigilant';

                array_push($parcoursArray, $parcours[$value]['onBoardingProfile']);
            }
        }

        $uniqueParcoursArray = array_unique($parcoursArray);
        $company = $this->em->getRepository(Company::class)->find($idCompany);

        if ($date) {
            $dateFrom = $date[0]['dateFrom']->format('d/m/y');
            $dateTo = $date[0]['dateTo']->format('d/m/y');
            $contractType = $date[0]['name'] === 'TYPE_USER_LICENSE' ? 'Utilisateur / Mois' : 'Groupe / an';
        } else {
            $dateFrom = '--/--/--';
            $dateTo = '--/--/--';
            $contractType = '-----';
        }

        $companyInformation[] = array(
            "id" => $idCompany,
            "name" => $company->getName(),
            'typeContract' => $contractType,
            "beginContract" => $dateFrom,
            "endContract" => $dateTo,
            "number_users" => $usersNumber,
            "users_active" => $usersOnBorder,
            "formations" => $formationsArray,
            "parcours" => $uniqueParcoursArray
        );

        return $companyInformation;
    }
}
