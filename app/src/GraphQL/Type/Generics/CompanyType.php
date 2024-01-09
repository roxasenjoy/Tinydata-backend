<?php


namespace App\GraphQL\Type\Generics;

use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class CompanyType extends AbstractObjectType
{
    public function build($config)
    {
        $config->addFields(
            [
                'id'    => new IntType(),
                'name'  => new StringType(),
            ]
        );
    }
}
