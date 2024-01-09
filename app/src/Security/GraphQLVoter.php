<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Youshido\GraphQLBundle\Security\Manager\SecurityManagerInterface;

class GraphQLVoter extends Voter
{

    /**
     * @inheritdoc
     */
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, [
            SecurityManagerInterface::RESOLVE_ROOT_OPERATION_ATTRIBUTE
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {

        if (SecurityManagerInterface::RESOLVE_ROOT_OPERATION_ATTRIBUTE == $attribute &&
            (
            in_array(
                $subject->getName(),
                array('__schema', 'forgotPassword', 'resetPassword', 'login', 'setPassword', 'signUpTinydata')
            )
            )
        ) {
            return true;
        }

        $authentificatedUser = $token->getUser();

        if (!$authentificatedUser instanceof User) {
            return false;
        }

        return true;
    }
}
