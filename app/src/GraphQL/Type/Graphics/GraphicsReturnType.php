<?php


namespace App\GraphQL\Type\Graphics;

use App\GraphQL\Type\Granularity\MatrixType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class GraphicsReturnType extends AbstractObjectType
{
    public function build($config)
    {
        $config->addFields(
            [
                'data'          => new StringType(),
            ]
        );
    }
}
