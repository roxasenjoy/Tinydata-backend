<?php

namespace App\GraphQL\Type;


use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class ProfileType extends AbstractObjectType
{

    /**
     * @param ObjectTypeConfig $config
     *
     * @return mixed
     */
    public function build($config)
    {
        $config->addFields([
            'id'                => new IntType(),
            'jobTitle'          => new StringType(),
            'domain'            => new StringType(),
            'score'             => new IntType(),

        ]);
    }
}
