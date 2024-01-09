<?php
namespace App\GraphQL\Mutation;

use App\GraphQL\Mutation\Token\ForgotPassword;
use App\GraphQL\Mutation\Token\Login;
use App\GraphQL\Mutation\Token\ResetPassword;
use App\GraphQL\Mutation\Token\SetPassword;
use App\GraphQL\Mutation\Token\SignUpTinydata;
use App\GraphQL\Mutation\User\DayConnection;
use App\GraphQL\Mutation\User\InitializeAccount;
use App\GraphQL\Mutation\User\UpdatePassword;
use App\GraphQL\Mutation\User\VerifyUser;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

class MutationType extends AbstractObjectType
{

    /**
     * @param ObjectTypeConfig $config
     *
     * @return mixed
     */
    public function build($config)
    {
        $config->addFields(
            [
                new Login(),
                new ForgotPassword(),
                new ResetPassword(),
                new SetPassword(),
                new SignUpTinydata(),
                new InitializeAccount(),
                new VerifyUser(),
                new UpdatePassword(),

                // Security
                new DayConnection()
            ]
        );
    }
}
