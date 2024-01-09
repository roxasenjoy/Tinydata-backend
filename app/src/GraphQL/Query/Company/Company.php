<?php


namespace App\GraphQL\Query\Company;

use App\GraphQL\Type\CompanyType;
use App\GraphQL\Type\Request\UserInt;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;

class Company extends AbstractContainerAwareField
{
    public function build(FieldConfig $config)
    {
        $config->addArguments(
            [
                'joinCompany' => new BooleanType()
            ]
        );
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        $companyResolver = $this->container->get('resolver.company');
        return $companyResolver->getCompanies($args['joinCompany']);
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return new ListType(new CompanyType());
    }
}
