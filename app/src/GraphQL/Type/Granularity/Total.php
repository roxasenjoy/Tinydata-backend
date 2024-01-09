<?php

namespace App\GraphQL\Type\Granularity;

use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class Total extends AbstractObjectType
{
    public function build($config)
    {
        $config->addFields(
            [
                'total'        => new IntType(),
            ]
        );
    }
}
