<?php

namespace App\GraphQL\Mutation\Token;

use App\GraphQL\Type\UserType;
use PhpOffice\PhpSpreadsheet\Calculation\Logical\Boolean;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;

class Login extends AbstractContainerAwareField
{

    public function build(FieldConfig $config)
    {
        $config->addArguments(
            [
            // login with email or phone
            'login' => new NonNullType(new StringType()),
            'password' => new NonNullType(new StringType()),
            'passwordRemember' => new BooleanType(),
            ]
        );
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        return $this->container->get('resolver.token')->login(trim($args['login']), $args['password'], $args['passwordRemember']);
    }

    /**
     * @return AbstractObjectType|AbstractType
     */
    public function getType()
    {
        return new UserType();
    }
}
