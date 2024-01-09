<?php

namespace App\GraphQL\Mutation\User;

use App\GraphQL\Type\UserType;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;

class UpdatePassword extends AbstractContainerAwareField
{

    public function build(FieldConfig $config)
    {
        $config->addArguments([
            'oldPassword' => new NonNullType(new StringType()),
            'newPassword' => new NonNullType(new StringType())
        ]);
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        return $this->container->get('resolver.token')->updateUserPassword($args['oldPassword'], $args['newPassword']);
    }

    /**
     * @return AbstractObjectType|AbstractType
     */
    public function getType()
    {
        return new UserType();
    }
}
