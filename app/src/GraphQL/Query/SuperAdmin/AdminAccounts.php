<?php

namespace App\GraphQL\Query\SuperAdmin;

use App\GraphQL\Type\Request\CompanyInt;
use App\GraphQL\Type\Request\UserInt;
use App\GraphQL\Type\Settings\AccountType;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;

class AdminAccounts extends AbstractContainerAwareField
{

    public function build(FieldConfig $config)
    {
        $config->addArguments(
            [
                'idAccount'     => new UserInt(),
                'companyId'     => new CompanyInt(),
                'searchText'    => new StringType()
            ]
        );
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        if(!array_key_exists('idAccount', $args)){
            $args['idAccount'] = null;
        }
        if(!array_key_exists('companyId', $args)){
            $args['companyId'] = null;
        }
        if(!array_key_exists('searchText', $args)){
            $args['searchText'] = null;
        }
        return $this->container->get('resolver.admin')->getAccounts($args['idAccount'], $args['companyId'], $args['searchText']);
    }

    /**
     * @return AbstractObjectType|AbstractType
     */
    public function getType()
    {
        return new ListType(new AccountType());
    }
}
