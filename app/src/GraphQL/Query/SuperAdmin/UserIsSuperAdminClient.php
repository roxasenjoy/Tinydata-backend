<?php

namespace App\GraphQL\Query\SuperAdmin;

use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;
use App\Entity\User;

class UserIsSuperAdminClient extends AbstractContainerAwareField
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
        return $this->container->get('resolver.roles')->verifyUserPermission(User::ROLE_USER_ADMIN);
    }

    /**
     * @return AbstractObjectType|AbstractType
     */
    public function getType()
    {
        return new BooleanType();
    }
}
