<?php

namespace App\GraphQL\Type;

use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

class PermissionType extends AbstractObjectType
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
            'id'                => new NonNullType(new IdType()),
            'permission_type'   => new StringType(),
            'permission_name'   => new StringType(),
            ]
        );
    }
}
