<?php


namespace App\GraphQL\Type;

use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class CompaniesNamesType extends AbstractObjectType
{
    /**
     * @inheritDoc
     * @throws ConfigurationException
     */
    public function build($config)
    {
        $config->addFields(
            [
                'inCompany' => new BooleanType(),
            ]
        );
    }
}
