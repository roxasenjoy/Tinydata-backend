<?php

namespace App\GraphQL\Type\Granularity;

use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class ContentType extends AbstractObjectType
{
    public function build($config)
    {
        $config->addFields(
            [
                'id' => new IntType(),
                'title' => new StringType(),
                'memo_question' => new StringType(),
                'depth' => new StringType(),
                'averageContentValid' => new IntType()
            ]
        );
    }
}
