<?php

namespace App\GraphQL\Query\Settings\Account;

use App\GraphQL\Type\Request\UserInt;
use App\GraphQL\Type\Settings\AccountType;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;

class Account extends AbstractContainerAwareField
{

    public function build(FieldConfig $config)
    {
        $config->addArguments(
            [
                'idAccount'    => new UserInt(),
            ]
        );
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        if(!array_key_exists("idAccount", $args)){
            $args["idAccount"] = null;
        }
        return $this->container->get('resolver.settings')->getAccount($args['idAccount']);
    }

    /**
     * @return ListType
     */
    public function getType()
    {
        return new ListType(new AccountType());
    }
}
