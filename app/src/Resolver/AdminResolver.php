<?php

namespace App\Resolver;

use App\Entity\Company;
use App\Entity\User;
use App\Service\UserService;


class AdminResolver extends AbstractResolver
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        //Check if user authed is superAdmin while using this Resolver
        $userService->userIsSuperAdmin();

        $this->userService = $userService;
    }

    public function getAccounts($userId, $companyId, $searchText)
    {
        $accounts = $this->em->getRepository(User::class)->getAccounts($userId, $companyId, $searchText);

        foreach($accounts as $key=>$account){
            // $accounts[$key]["roles"] = $this->em->getRepository(UserRoles::class)->findRolesByUserId($account['id']);
            $accounts[$key]["organisation"] = $this->em->getRepository(Company::class)->findOneBy(array("id" => $account['company_id']));
        }

        return $accounts;
    }

    // public function getRoles($roleId, $companyId, $searchText){

    //     $roles = $this->em->getRepository(Roles::class)->getRoles($roleId, $companyId, $searchText);
    //     foreach($roles as $key => $value){
    //         $roles[$key]['permissions'] = $this->tinydataPermission->getPermissionsFromRole($value['id']);
    //         $roles[$key]['company'] = $this->companyRepository->findOneBy(array("id" => $roles[$key]['companyId']));
    //     }
    //     return $roles;

    // }
}
