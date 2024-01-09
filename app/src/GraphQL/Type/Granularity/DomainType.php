<?php

namespace App\GraphQL\Type\Granularity;

use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class DomainType extends AbstractObjectType
{
    public function build($config)
    {
        $config->addFields(
            [
                'id'        => new IntType(),
                'domainID'  => new IntType(),
                'title'     => new StringType(),
                'name'      => new StringType(),
                'depth'     => new StringType(),
                'value'     => new IntType(),
                'skills'    => new ListType(new SkillType()),
                'firstname' => new StringType(),
                'lastname'  => new StringType(),
                'email'     => new StringType(),
                'level'     => new IntType(),
                'companyName'     => new StringType(),
            ]
        );
    }
}
