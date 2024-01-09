<?php

namespace App\Resolver;

use App\Entity\Company;
use App\Entity\User;
use App\Service\StorageService;
use App\Service\UserService;

class SettingsResolver extends AbstractResolver
{

    /**
     * @var StorageService
     */
    private $userService;

    public function __construct(UserService $userService){
        $this->userService = $userService;
    }

    /*************************
     * Tous les comptes
     ************************/
    public function getAccounts($id = null){

        $accounts = $this->em->getRepository(User::class)->getAccounts($id);

        foreach($accounts as $key=>$account){
            $accounts[$key]["organisation"] = $this->em->getRepository(Company::class)->findOneBy(array("id" => $account['company_id']));
        }

        return $accounts;
    }

    public function getAccount($id = null){
        if(!$id){
            $id = $this->userService->getUser()->getId();
        }
        return $this->getAccounts($id);
    }


    public function updateAccount(){

    }

    public function deleteAccount(){

    }
}
