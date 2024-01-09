<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

/**
 * Documentation du code : https://numa-bord.com/miniblog/symfony-5-utiliser-la-fonction-isgranted-sur-nimporte-quel-objet-utilisateur/
 * 
 * Permet de déterminer tous les rôles de l'utilisateur en se basant sur le fichier security.yaml.
 * Ce n'était pas possible de vérifier le rôle de l'utilisateur avant que celui-ci soit connecté
 */
class SecurizerService {

    private $accessDecisionManager;

    public function __construct(AccessDecisionManagerInterface $accessDecisionManager) {
        $this->accessDecisionManager = $accessDecisionManager;
    }

    public function isGranted(User $user, $attribute, $object = null) {
        $token = new UsernamePasswordToken($user, 'none', 'none', $user->getRoles());
        return ($this->accessDecisionManager->decide($token, [$attribute], $object));
    }

}