<?php

namespace App\GraphQL\Type;

use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\DateTimeType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class CompanyInformationType extends AbstractObjectType
{
    /**
     * @inheritDoc
     * @throws ConfigurationException
     */
    public function build($config)
    {
        $config->addFields(
            [
                'id' => new NonNullType(new IdType()),
                'name' => new StringType(),
                'typeContract' => new StringType(),
                'beginContract' => new StringType(),
                'endContract' => new StringType(),
                'number_users' => new IntType(),
                'users_active' => new IntType(),
                'formations' => new ListType(new StringType()),
                'parcours' => new ListType(new StringType()),
            ]
        );
    }
}
