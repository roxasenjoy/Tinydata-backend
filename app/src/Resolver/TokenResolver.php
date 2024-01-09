<?php

namespace App\Resolver;

use App\Entity\User;
use App\Exception\ApiProblem;
use App\Exception\ApiProblemException;
use App\Service\SecurizerService;
use App\Service\UserService;
use DateTime;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Security;

class TokenResolver extends AbstractResolver
{
    protected $userService;
    private $jwtEncoder;
    private $tokenStorage;

    const EMAIL_TYPE = 'email';
    const PASSWORD_TYPE = 'password';
    const COMPANY_TYPE = 'companyName';

    public function __construct(
        UserService $userService, 
        JWTEncoderInterface $jwtEncoder, 
        TokenStorageInterface $tokenStorage
        )
    {
        $this->userService = $userService;
        $this->jwtEncoder = $jwtEncoder;
        $this->tokenStorage = $tokenStorage;

    }

    public function login($login, $password)
    {
        if (!$login || !$password) {
            $apiProblem = new ApiProblem(Response::HTTP_OK, ApiProblem::MISSING_ARGUMENTS);
            throw new ApiProblemException($apiProblem);
        }

        $user = $this->userService->authenticateUser($login, $password);
        if (!$user) {
            throw new BadCredentialsException("Identifiant ou mot de passe incorrect");
        }

        $token = $this->generateToken($user);

        $user->setToken($token);

        return $user;
    }

    public function forgotPassword($email){
        if (!$email) {
            $apiProblem = new ApiProblem(Response::HTTP_OK, ApiProblem::MISSING_ARGUMENTS);
            throw new ApiProblemException($apiProblem);
        }
        return $this->userService->forgotPassword($email);
    }

    public function resetPassword($email, $validationCode, $password){
        if (!$email || !$validationCode || !$password) {
            $apiProblem = new ApiProblem(Response::HTTP_OK, ApiProblem::MISSING_ARGUMENTS);
            throw new ApiProblemException($apiProblem);
        }
        return $this->userService->resetPassword($email, $validationCode, $password);
    }

    public function setPassword($email, $password){
        if (!$email || !$password) {
            $apiProblem = new ApiProblem(Response::HTTP_OK, ApiProblem::MISSING_ARGUMENTS);
            throw new ApiProblemException($apiProblem);
        }
        return $this->userService->setPassword($email, $password);
    }

    public function initializeAccount($oldPassword, $newPassword){
        $userAuthenticated = $this->userService->getAuthenticatedUser();
        $user = $this->em->getRepository(User::class)->findOneBy(array("email" => $userAuthenticated["email"]));
        if(!$user->getUserClient()->getCgu() && !$user->getInscriptionDate()){

            $res = $this->updateUserPassword($oldPassword, $newPassword);

            $user->getUserClient()->setCgu(true);
            $user->setInscriptionDate(new DateTime());
            $this->em->persist($user);
            $this->em->flush();
            
            return $res;
        }
        else{
            $apiProblem = new ApiProblem(Response::HTTP_FORBIDDEN, ApiProblem::INVALID_FORM_DATA);
            throw new ApiProblemException($apiProblem);
        }
    }

    public function verifyUserInformation($userId, $value, $type){


        $user = $this->userService->verifyUserInformation($userId, $value, $type);

        if($type === self::EMAIL_TYPE || $type === self::PASSWORD_TYPE){
            $token =  $this->generateToken($user);
            $user->setToken($token);
        }
         
        return $user;

    }

    public function updateUserPassword($oldPassword, $newPassword){
        $user = $this->userService->updatePassword($oldPassword, $newPassword);
        $token =  $this->generateToken($user);
        $user->setToken($token);
            
        return $user;
    }

    public function generateToken($user){
        $token = $this->jwtEncoder->encode(
            [
                'email' => $user->getUsername(),
                'lastName' => $user->getLastName(),
                'firstName' => $user->getFirstName(),
                'token' => $user->getToken(),
                // token expire after a month
                'exp' => time() + 2592000,
            ]
        );

        return $token;
    }

    /**
     * Récupération du token TinySuite
     */
    public function getTinysuiteToken(){
        return $this->userService->getUser()->getToken();
    }

    public function dayConnection($args){
        try {
            // check isNewDevice via curl request to TINYCOACHING BACKEND
            $isNewDevice = $this->userService->dayConnection($args);
            return $isNewDevice;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

}
