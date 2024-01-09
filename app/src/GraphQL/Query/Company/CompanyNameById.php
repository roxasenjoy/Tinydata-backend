<?php


namespace App\GraphQL\Query\Company;

use App\GraphQL\Type\CompanyNameType;
use App\GraphQL\Type\Request\CompanyInt;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;

class CompanyNameById extends AbstractContainerAwareField
{
    public function build(FieldConfig $config)
    {
        $config->addArguments(
            [
                'id' => new CompanyInt()
            ]
        );
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        $companyResolver = $this->container->get('resolver.company');
        return $companyResolver->getCompanyNameById($args['id']);
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return new CompanyNameType();
    }
}
