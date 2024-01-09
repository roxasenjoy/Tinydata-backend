<?php


namespace App\GraphQL\Type\Granularity;

use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class MatrixType extends AbstractObjectType
{
    public function build($config)
    {
        $config->addFields(
            [
                'id'                => new IntType(),
                'matrixID'          => new IntType(),
                'title'             => new StringType(),
                'name'              => new StringType(),
                'depth'             => new StringType(),
                'value'             => new IntType(),
                'domains'           => new ListType(new DomainType()),
                'firstname'         => new StringType(),
                'lastname'          => new StringType(),
                'email'             => new StringType(),
                'level'             => new IntType(),
                'companyName'       => new StringType(),
                'hasData'           => new BooleanType()
            ]
        );
    }
}
