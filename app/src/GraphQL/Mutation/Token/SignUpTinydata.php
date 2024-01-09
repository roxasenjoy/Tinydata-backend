<?php


namespace App\GraphQL\Mutation\Token;

use App\GraphQL\Type\UserType;
use App\Resolver\TokenResolver;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;

class SignUpTinydata extends AbstractContainerAwareField
{
    public function build(FieldConfig $config)
    {
        $config->addArguments(
            [
                'email' => new NonNullType(new StringType()),
                'firstName' => new StringType(),
                'lastName' => new StringType(),
                'phone' => new StringType(),
                'cgu' => new BooleanType()

            ]
        );
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        /** @var  TokenResolver  $tokenResolver */
        $tokenResolver = $this->container->get('resolver.token');

        return $tokenResolver->signUpTinydata($args);
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return new BooleanType();
    }
}
