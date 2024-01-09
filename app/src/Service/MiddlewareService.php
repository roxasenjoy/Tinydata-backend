<?php

namespace App\Service;

use App\Entity\Matrix;
use App\Entity\User;
use App\Exception\ApiProblem;
use App\Exception\ApiProblemException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class MiddlewareService
{
    private $em;
    private $userService;
    private $globalFilterService;


    public function __construct(
        EntityManagerInterface $em, 
        UserService $userService, 
        GlobalFilterService $globalFilterService)
    {
        $this->em = $em;
        $this->userService = $userService;
        $this->globalFilterService = $globalFilterService;
    }

    public function checkCompanyArg(Int $id){
        if($this->userService->userIsSuperAdmin(false)){
            return true;
        }
        $companyIds = $this->globalFilterService->getAllCompaniesForUser(false);

        return in_array($id, $companyIds);
    }

    public function checkUserArg(Int $id){
        if($this->userService->userIsSuperAdmin(false)){
            return true;
        }

        $user = $this->em->getRepository(User::class)->getUsers($id);
        return sizeof($user) > 0;
    }

    public function checkMatrixArg(Int $id){
        if($this->userService->userIsSuperAdmin(false)){
            return true;
        }
        $matrixesIds = $this->globalFilterService->getAllMatrixes();
        return in_array($id, $matrixesIds);
    }

    public function checkDomainArg(Int $id){
        //SuperAdmin Checked on matrix function
        $matrix = $this->em->getRepository(Matrix::class)->findMatrixByDomain($id);
        return $this->checkMatrixArg($matrix->getId());
    }

    public function checkSkillArg(Int $id){
        //SuperAdmin Checked on matrix function
        $matrix = $this->em->getRepository(Matrix::class)->findMatrixBySkill($id);
        return $this->checkMatrixArg($matrix->getId());
    }

    public function checkThemeArg(Int $id){
        //SuperAdmin Checked on matrix function
        $matrix = $this->em->getRepository(Matrix::class)->findMatrixByTheme($id);
        return $this->checkMatrixArg($matrix->getId());
    }

    public function checkAcquisitionArg(Int $id){
        //SuperAdmin Checked on matrix function
        $matrix = $this->em->getRepository(Matrix::class)->findMatrixByAcquisition($id);
        return $this->checkMatrixArg($matrix->getId());
    }

    public function checkRoleArg(Int $id){
        if($this->userService->userIsSuperAdmin(false)){
            return true;
        }
        $roleIds = $this->globalFilterService->getAllRoles();

        return in_array($id, $roleIds);
    }

    public function throwError(){
        $apiProblem = new ApiProblem(Response::HTTP_UNAUTHORIZED, ApiProblem::UNAUTHORIZED);
        throw new ApiProblemException($apiProblem);
    }
}
