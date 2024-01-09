<?php


namespace App\Resolver;

use App\Repository\CompanyRepository;
use App\Service\UserService;

class RolesResolver extends AbstractResolver
{

    /**
     * @var UserService
     */
    private $userService;

    /**
     * CompanyResolver constructor.
     * @param CompanyRepository $companyRepository
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function verifyUserPermission($role){
        return $this->userService->userIsSuperAdmin(false, $role);
    }

    

}
