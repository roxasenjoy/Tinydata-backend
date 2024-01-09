<?php

namespace App\GraphQL\Query\User;

use App\GraphQL\Type\UserType;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;

class User extends AbstractContainerAwareField
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
        return $this->container->get('resolver.user')->getUser();
    }

    /**
     * @return AbstractObjectType|AbstractType
     */
    public function getType()
    {
        return new UserType();
    }
}
