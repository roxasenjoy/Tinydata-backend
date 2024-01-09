<?php

namespace App\GraphQL\Type;

use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class DataType extends AbstractObjectType
{
    /**
     * @inheritDoc
     * @throws ConfigurationException
     */
    public function build($config)
    {
        $config->addFields(
            [
                /* Nom des granularités */
                'nameMatrix'        => new StringType(),
                'nameDomain'        => new StringType(),
                'nameSkill'         => new StringType(),
                'nameTheme'         => new StringType(),
                'nameAcquis'        => new StringType(),
                'nameContent'       => new StringType(),
                /* Autres */
                'category'          => new IntType(),
                'categoryName'      => new StringType(),
                'id'                => new IntType()
            ]
        );
    }
}
