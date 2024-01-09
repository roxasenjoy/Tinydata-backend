<?php


namespace App\GraphQL\Type\Generics;

use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;

class UserClientType extends AbstractObjectType
{
    public function build($config)
    {
        $config->addFields(
            [
                'id'        => new IntType(),
                'user'      => new UserType(),
                'company'   => new CompanyType()
            ]
        );
    }
}
