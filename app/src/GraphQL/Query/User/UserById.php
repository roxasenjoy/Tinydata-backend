<?php

namespace App\GraphQL\Query\User;

use App\GraphQL\Type\Request\UserInt;
use App\GraphQL\Type\UserType;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;

class UserById extends AbstractContainerAwareField
{

    public function build(FieldConfig $config)
    {
        $config->addArguments(
            [
                'id' => new UserInt()
            ]
        );
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        return $this->container->get('resolver.user')->getUserById($args['id']);
    }

    /**
     * @return AbstractObjectType|AbstractType
     */
    public function getType()
    {
        return new UserType();
    }
}
