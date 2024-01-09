<?php

namespace App\Resolver;

use App\Entity\User;
use App\Service\UserService;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class UserResolver extends AbstractResolver
{
    protected $userService;
    private $jwtEncoder;
    private $requestStack;

    public function __construct(UserService $userService, JWTEncoderInterface $jwtEncoder, RequestStack $requestStack, TokenStorageInterface $tokenStorage)
    {
        $this->userService = $userService;
        $this->jwtEncoder = $jwtEncoder;
        $this->requestStack = $requestStack;
        $this->tokenStorage = $tokenStorage;
    }

    public function getUser()
    {
        $request = $this->requestStack->getCurrentRequest();
        $extractor = new AuthorizationHeaderTokenExtractor('Bearer', 'Authorization');
        $token = $extractor->extract($request);
        if (!$token) {
            return null;
        }
        $jwt = $this->jwtEncoder->decode($token);

        return $this->userService->getUserByJWT($jwt);
    }

    public function getUserById($id){
        $userById = $this->em->getRepository(User::class)->findOneBy(array("id" => $id)); 
        return $userById;
    }

    public function getUsers($userId, $searchText)
    {
        $accounts = $this->em->getRepository(User::class)->getUsers($userId, null, $searchText);

        return $accounts;
    }
}
