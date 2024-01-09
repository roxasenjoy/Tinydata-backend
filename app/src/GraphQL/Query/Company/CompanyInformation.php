<?php


namespace App\GraphQL\Query\Company;

use App\GraphQL\Type\CompanyInformationType;
use App\GraphQL\Type\Request\CompanyInt;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;

class CompanyInformation extends AbstractContainerAwareField
{
    public function build(FieldConfig $config)
    {
        //Return la recherche du user
        $config->addArguments([
            'idCompany' => new CompanyInt(),
        ]);
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        $companyResolver = $this->container->get('resolver.company');
        return $companyResolver->getCompaniesInformation($args['idCompany']);
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return new ListType(new CompanyInformationType());
    }
}
