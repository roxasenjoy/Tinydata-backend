<?php

namespace App\GraphQL\Query\SuperAdmin;

use App\GraphQL\Type\Request\CompanyInt;
use App\GraphQL\Type\Request\RoleInt;
use App\GraphQL\Type\RoleType;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;

class AdminRoles extends AbstractContainerAwareField
{

    public function build(FieldConfig $config)
    {
        $config->addArguments(
            [
                'id'            => new RoleInt(),
                'companyId'     => new CompanyInt(),
                'searchText'    => new StringType()
            ]
        );
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        if(!array_key_exists('id', $args)){
            $args['id'] = null;
        }
        if(!array_key_exists('companyId', $args)){
            $args['companyId'] = null;
        }
        if(!array_key_exists('searchText', $args)){
            $args['searchText'] = null;
        }
        return $this->container->get('resolver.admin')->getRoles($args['id'], $args['companyId'], $args['searchText']);
    }

    /**
     * @return AbstractObjectType|AbstractType
     */
    public function getType()
    {
        return new ListType(new RoleType());
    }
}
