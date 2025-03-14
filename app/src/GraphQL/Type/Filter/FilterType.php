<?php

namespace App\GraphQL\Type\Filter;

use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\DateTimeType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class FilterType extends AbstractObjectType
{
    public function build($config)
    {

        // Élément de mon filtre
        $config->addFields([
            'id'     => new IntType(),
            'date'   => new DateTimeType(),
            'parcours' => new StringType(),
            'entreprise' => new StringType()

        ]);
    }
}
