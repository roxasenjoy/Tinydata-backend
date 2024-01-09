<?php

namespace App\GraphQL\Mutation\Token;

use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;

class ResetPassword extends AbstractContainerAwareField
{

    public function build(FieldConfig $config)
    {
        $config->addArguments(
            [
                'email' => new NonNullType(new StringType()),
                'validationCode' => new NonNullType(new StringType()),
                'password' => new NonNullType(new StringType()),
            ]
        );
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        return $this->container->get('resolver.token')->resetPassword(
            $args['email'],
            $args['validationCode'],
            $args['password']        
        );
    }

    /**
     * @return AbstractObjectType|AbstractType
     */
    public function getType()
    {
        return new BooleanType();
    }
}
