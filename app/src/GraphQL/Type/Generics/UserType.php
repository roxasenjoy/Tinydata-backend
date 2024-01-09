<?php


namespace App\GraphQL\Type\Generics;

use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class UserType extends AbstractObjectType
{
    public function build($config)
    {
        $config->addFields(
            [
                'id'        => new IntType(),
                'firstName' => new StringType(),
                'lastName'  => new StringType(),
                'email'     => new StringType(),
            ]
        );
    }
}
