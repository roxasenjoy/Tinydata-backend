<?php

namespace App\Service;

use App\Exception\ApiProblem;
use App\Exception\ApiProblemException;
use App\Resolver\TokenResolver;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Security;

class UserService
{
    private $em;
    private $hostAPIURL;
    private $requestStack;

    const FIRSTNAME = 'firstName';
    CONST LASTNAME = 'lastName';
    const JOB_TITLE = 'jobTitle';
    const EMAIL_TYPE = 'email';
    CONST PASSWORD_TYPE = 'password';
    CONST ORGANISATION = 'companyName';

    public function __construct(
        EntityManagerInterface $em, 
        JWTEncoderInterface $jwtEncoder, 
        RequestStack $requestStack,
        SecurizerService $securizerService
        )
    {
        $this->hostAPIURL = $_ENV['AUTH_API_URL'];
        $this->em = $em;
        $this->jwtEncoder = $jwtEncoder;
        $this->requestStack = $requestStack;
        $this->securizerService = $securizerService;

    }

    public function userIsSuperAdmin($shouldThrowError = true, $role = 'ROLE_ADMIN'){
        $user = $this->getUser();

        /**
         * ROLE_ADMIN = Administrateur Tinycoaching
         * Si l'utilisateur ne possède pas ce rôle, il n'aura pas accès à toutes les entreprises du système.
         */
        $hasAccess = $this->securizerService->isGranted($user, $role);

        if(!$hasAccess){
            if($shouldThrowError){
                $apiProblem = new ApiProblem(Response::HTTP_UNAUTHORIZED, ApiProblem::UNAUTHORIZED);
                throw new ApiProblemException($apiProblem);
            }
            return false;
        } 

        return true;
    }

    public function authenticateUser($login, $password){

        $query = "
        mutation{
            login(login:\"".addslashes($login)."\", password:\"".addslashes($password)."\"){
                token
                firstName
                lastName
                email
                roles
            }
        }";

        $json = json_encode(['query' => $query]);

        $response = $this->makeUserAPIRequest($json);
        
        if(array_key_exists("errors", $response)){
            throw new BadCredentialsException($response["errors"][0]["message"]);
        }
        $data = $this->returnData($response, "login");

        return $this->associateDataUser($data);
    }

    public function forgotPassword($email){
        $query = "
        mutation{
            forgotPassword(email:\"".addslashes($email)."\", context:\"Tinydata\")
        }";
        $json = json_encode(['query' => $query]);
        $response = $this->makeUserAPIRequest($json);

        return $this->returnData($response, 'forgotPassword');
    }


    public function dayConnection($args){
        $query = "
        mutation {
            dayConnection(
            location:\"". addSlashes($args['location'])."\",
            latitude:". $args['latitude'].",
            longitude:". $args['longitude'].",
            browser:\"". addSlashes($args['browser'])."\",
            browser_version:\"". addSlashes($args['browser_version'])."\",
            device:\"". addSlashes($args['device'])."\",
            os:\"". addSlashes($args['os'])."\",
            os_version:\"". addSlashes($args['os_version'])."\",
            userAgent:\"". addSlashes($args['userAgent'])."\",
            firebaseToken:\"".addSlashes($args['firebaseToken'])."\",
            context:\"". addSlashes($args['context'])."\",
          )
        }
        ";
        $json = json_encode(['query' => $query]);
        $response = $this->makeUserAPIRequest($json,true);

        return $this->returnData($response, 'dayConnection');
    }

    public function resetPassword($email, $validationCode, $password){
        $query = "
        mutation{
            resetPassword(email:\"".addslashes($email)."\", validationCode:\"".addslashes($validationCode)."\", password:\"".addslashes($password)."\", context:\"Tinydata\")
        }";
        $json = json_encode(['query' => $query]);
        $response = $this->makeUserAPIRequest($json);

        return $this->returnData($response, 'resetPassword');
    }

    public function updatePassword($oldPassword, $newPassword){
        $query = "
        mutation{
            updatePassword(oldPassword:\"".addslashes($oldPassword)."\", newPassword:\"".addslashes($newPassword)."\", context:\"Tinydata\"){
                token
                email
                firstName
                lastName
            }
        }";
        $json = json_encode(['query' => $query]);
        $response = $this->makeUserAPIRequest($json, true);

        if(array_key_exists('errors', $response)){
            $apiProblem = new ApiProblem(Response::HTTP_UNAUTHORIZED, ApiProblem::INVALID_FORM_DATA);
            throw new ApiProblemException($apiProblem);
        }
        $data = $this->returnData($response, 'updatePassword');
        return  $this->associateDataUser($data);
    }

    public function setPassword($email, $password){
        $query = "
        mutation{
            setPassword(email:\"".addslashes($email)."\", password:\"".addslashes($password)."\", context:\"Tinydata\"){
                token
                firstName
                lastName
                email
                permissions{
                    permission_type
                    permission_name
                }
            }
        }";
        $json = json_encode(['query' => $query]);
        $response = $this->makeUserAPIRequest($json);

        $data = $this->returnData($response, "setPassword");

        return $this->associateDataUser($data);
    }

    public function signUpTinyData($args){
        $email          = addslashes($args["email"]) ?: "";
        $firstName      = addslashes($args["firstName"]) ?: "";
        $lastName       = addslashes($args["lastName"]) ?: "";
        $phone          = addslashes($args["phone"]) ?: "";
        $cgu            = "false";
        $companyId      = addslashes($args["organisation"]) ?: "";
        $tiny_token     = getenv("TINYCOACHING_TOKEN");

        $query = "
        mutation{
            signUpTinydata(email:\"$email\", firstName:\"$firstName\", lastName:\"$lastName\", phone:\"$phone\", cgu:$cgu, companyId:$companyId, token: \"$tiny_token\"){
                token
                firstName
                lastName
                email
                permissions{
                    permission_type
                    permission_name
                }
            }
        }";

        $json = json_encode(['query' => $query]);
        $response = $this->makeUserAPIRequest($json, true);

        if(array_key_exists('errors', $response)){
            switch($response['errors'][0]['code']){
                case 1006:
                    $apiProblem = new ApiProblem(Response::HTTP_UNAUTHORIZED, ApiProblem::EMAIL_TAKEN);
                    throw new ApiProblemException($apiProblem);
                case 1010:
                    $apiProblem = new ApiProblem(Response::HTTP_INTERNAL_SERVER_ERROR, ApiProblem::ALREADY_EXISTS);
                    throw new ApiProblemException($apiProblem);
                    break; 
                case 1001:
                default:
                    $apiProblem = new ApiProblem(Response::HTTP_INTERNAL_SERVER_ERROR, ApiProblem::SSO_COMMUNICATION_FAILED);
                    throw new ApiProblemException($apiProblem);
            }
        }

        $data = $this->returnData($response, "signUpTinydata");

        return $data;
    }

    public function verifyUserInformation($userId, $value, $type){

        $tiny_token = getenv("TINYCOACHING_TOKEN");
 
        $query = "
              mutation{
                verifyUser(userId: $userId, value: \"$value\", type: \"$type\", token: \"$tiny_token\") {
                  firstName
                  lastName
                  profile{
                      jobTitle
                  }
                  companyName
                  email
                  token
                }
              }
            ";

        $json = json_encode(['query' => $query]);

        $response = $this->makeUserAPIRequest($json, true);
        $data = $this->returnData($response, "verifyUser");

        return  $this->associateDataUser($data, $type);
    }

    public function getAuthenticatedUser()
    {
        $request = $this->requestStack->getCurrentRequest();
        $extractor = new AuthorizationHeaderTokenExtractor('Bearer', 'Authorization');
        $token = $extractor->extract($request);

        if (!$token) {
            if($request->getPathInfo() === '/export'){
                $token = $request->query->get('token');
            } else {
                $apiProblem = new ApiProblem(Response::HTTP_UNAUTHORIZED, ApiProblem::SSO_COMMUNICATION_FAILED);
                throw new ApiProblemException($apiProblem);
            }
        }

        $jwt = $this->jwtEncoder->decode($token);

        return $jwt;
    }

    public function getUser(){
        $currentUser = $this->getAuthenticatedUser();
        return $this->em->getRepository(User::class)->findOneBy(["email" => $currentUser['email']]);
    }

    public function getUserCompany(){
        return $this->getUser()->getCompany();
    }

    public function getUserById($userId){
        return $this->em->getRepository(User::class)->find($userId);
    }
    public function getUserByJWT($data){
        //Should we check JWT Token to SSO here ?
        return $this->associateDataUser($data);
    }

    private function associateDataUser($data, $type = false){

        if($type === self::EMAIL_TYPE || $type === self::PASSWORD_TYPE ){
            if(!$data['email'] || !$data['lastName'] || !$data['firstName'] || !$data['token']){
                return false;
            }
        }
        
        $user = $this->em->getRepository(User::class)->findOneBy(["email" => $data['email']]);

        /**
         * Vérification du rôle de l'utilisateur qui souhaite se connecter.
         * Son rôle doit posséder à minima le rôle suivant : ROLE_TINYDATA
         */

        $hasAccess = $this->securizerService->isGranted($user, 'ROLE_TINYDATA');

        if(!$hasAccess){
            throw new BadCredentialsException("UNAUTHORIZED ACCESS TINYDATA");
        } 

        if($type === self::FIRSTNAME || $type === self::LASTNAME || $type === self::JOB_TITLE || $type === self::ORGANISATION){
            $user->setToken('.');
        } else {
            $user->setToken($data['token']);
        }

        if($type === self::EMAIL_TYPE || $type === self::PASSWORD_TYPE){
            $user   ->setUsername($data['email'])
                    ->setEmail($data['email'])
                    ->setLastName($data['lastName'])
                    ->setFirstName($data['firstName'])
                    ->setToken($data['token']);
        }

       
            
        return $user;
        

        
    }

    private function makeUserAPIRequest($parameters = null, $authenticated = false){
        //Contrôle si la variable d'environnement est bien paramétrée
        if(!$this->hostAPIURL || $this->hostAPIURL == ""){
            $apiProblem = new ApiProblem(Response::HTTP_INTERNAL_SERVER_ERROR, ApiProblem::SSO_COMMUNICATION_FAILED);
            throw new ApiProblemException($apiProblem);
        }

        $headers =  array(
            'User-Agent: PHP Script',
            'Content-Type: application/json;charset=utf-8',
        );

        if($authenticated){
            $user = $this->getAuthenticatedUser();
            array_push($headers, 'Authorization: Bearer ' . $user['token']);
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->hostAPIURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

        //For debugging, set HEADER AND VERBOSE TO TRUE
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);


        //On vérifie qu'il n'y ait pas d'erreur
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($statusCode < 200 || $statusCode >= 300)
        {
            $apiProblem = new ApiProblem(Response::HTTP_OK, ApiProblem::AUTH_API_CONNEXION);
            throw new ApiProblemException($apiProblem);
        }


        $content = json_decode($response, true);

        return $content;
    }

    private function returnData($data, $key){
        if(!$data){
            return false;
        }

        $data = $data['data'][$key];

        if(!$data){
            return false;
        }
        return $data;
    }

}
