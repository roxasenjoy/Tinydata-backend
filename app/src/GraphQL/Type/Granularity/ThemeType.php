<?php

namespace App\GraphQL\Type\Granularity;

use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class ThemeType extends AbstractObjectType
{
    public function build($config)
    {
        $config->addFields(
            [
                'id'            => new IntType(),
                'themeID'       => new IntType(),
                'title'         => new StringType(),
                'name'          => new StringType(),
                'depth'         => new StringType(),
                'level'         => new StringType(),
                'value'         => new StringType(),
                'firstname'     => new StringType(),
                'lastname'      => new StringType(),
                'email'         => new StringType(),
                'companyName'   => new StringType(),
            ]
        );
    }
}
