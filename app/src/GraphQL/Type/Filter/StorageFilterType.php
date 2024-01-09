<?php

namespace App\GraphQL\Type\Filter;

use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class StorageFilterType extends AbstractObjectType
{
    public function build($config)
    {

        // Élément de mon filtre
        $config->addFields([
            'matrixFilter' => new ListType(new StringType()),
            'domainFilter' => new ListType(new StringType()),
            'skillFilter' => new ListType(new StringType()),
            'themeFilter' => new ListType(new StringType()),
            'acquisFilter' => new ListType(new StringType()),
            'contentFilter' => new ListType(new StringType()),

            'organisationsFilter' => new ListType(new StringType()),

            'beginDate' => new StringType(),
            'endDate' => new StringType(),

            'idUser' => new IntType(),

        ]);
    }
}
