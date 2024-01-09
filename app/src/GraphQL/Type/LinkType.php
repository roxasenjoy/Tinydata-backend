<?php

namespace App\GraphQL\Type;

use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class LinkType extends AbstractObjectType
{
    /**
     * @inheritDoc
     * @throws ConfigurationException
     */
    public function build($config)
    {
        $config->addFields(
            [
                'source' => new IntType(),
                'target' => new IntType(),
            ]
        );
    }
}
