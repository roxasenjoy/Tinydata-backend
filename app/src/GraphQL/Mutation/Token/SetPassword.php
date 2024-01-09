<?php

namespace App\GraphQL\Mutation\Token;

use App\GraphQL\Type\UserType;
use App\Resolver\TokenResolver;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;

class SetPassword extends AbstractContainerAwareField
{

    public function build(FieldConfig $config)
    {
        $config->addArguments(
            [
                'email' => new NonNullType(new StringType()),
                'password' => new NonNullType(new StringType()),
            ]
        );
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        /** @var TokenResolver $tokenResolver */
        $tokenResolver = $this->container->get('resolver.token');

        return $tokenResolver->setPassword(
            $args['email'],
            $args['password']
        );
    }

    /**
     * @return AbstractObjectType|AbstractType
     */
    public function getType()
    {
        return new UserType();
    }



}
