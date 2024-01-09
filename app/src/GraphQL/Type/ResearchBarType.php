<?php

namespace App\GraphQL\Type;

use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class ResearchBarType extends AbstractObjectType
{

    /**
     * @param ObjectTypeConfig $config
     *
     * @return mixed
     * @throws \Youshido\GraphQL\Exception\ConfigurationException
     */
    public function build($config)
    {

        // Liste des éléments dont j'ai besoin pour la barre de recherche
        $config->addFields([
            'objectId'     => new IntType(),
            'title'  => new StringType(),
            'type'  => new StringType(),
        ]);
    }
}
