<?php


namespace App\GraphQL\Type;

use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\StringType;

class CompanyType extends AbstractObjectType
{
    /**
     * @inheritDoc
     * @throws ConfigurationException
     */
    public function build($config)
    {
        $config->addFields(
            [
                'id'        => new IdType(),
                'name'      => new StringType(),
                'childs'    => new ListType(new CompanyType()),
            ]
        );
    }
}
