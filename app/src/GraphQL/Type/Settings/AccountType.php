<?php

namespace App\GraphQL\Type\Settings;

use App\GraphQL\Type\CompanyType;
use App\GraphQL\Type\RoleType;
use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class AccountType extends AbstractObjectType
{

    /**
     * @param ObjectTypeConfig $config
     *
     * @return mixed
     */
    public function build($config)
    {
        $config->addFields([
            'id'                            => new IntType(),
            'firstName'                     => new StringType(),
            'lastName'                      => new StringType(),
            'email'                         => new StringType(),
            'organisation'                  => new CompanyType()
        ]);
    }
}
