<?php

namespace App\GraphQL\Mutation\User;

use App\GraphQL\Type\Request\UserInt;
use App\GraphQL\Type\UserType;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;
use App\GraphQL\Type\Request\RoleInt;

class VerifyUser extends AbstractContainerAwareField
{

    public function build(FieldConfig $config)
    {
        $config->addArguments([
            'value' => new StringType(),
            'type' => new NonNullType(new StringType()),
            'userId' => new NonNullType(new UserInt())
        ]);
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        return $this->container->get('resolver.token')->verifyUserInformation($args['userId'], $args['value'], $args['type']);
    }

    /**
     * @return AbstractObjectType|AbstractType
     */
    public function getType()
    {
        return new UserType();
    }
}
