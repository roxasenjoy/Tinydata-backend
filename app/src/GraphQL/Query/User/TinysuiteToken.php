<?php

namespace App\GraphQL\Query\User;

use App\GraphQL\Type\TinysuiteTokenType;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\ResolveInfo;

use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;

class TinysuiteToken extends AbstractContainerAwareField
{

    public function build(FieldConfig $config)
    {
        $config->addArguments(
            [
              
            ]
        );
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        return $this->container->get('resolver.token')->getTinysuiteToken();
    }

    public function getType()
    {
        return new StringType();
    }
}
