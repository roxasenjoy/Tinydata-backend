<?php


namespace App\GraphQL\Query\Company;

use App\GraphQL\Type\CompanyType;
use App\GraphQL\Type\Request\CompanyInt;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;

class CompanyObjectifs extends AbstractContainerAwareField
{
    public function build(FieldConfig $config)
    {
        $config->addArguments(
            [
                'objectifsFilter' => new ListType(new StringType())
            ]
        );
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        $companyResolver = $this->container->get('resolver.company');
        return $companyResolver->getCompanyObjectifs($args['objectifsFilter']);
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return new ListType(new CompanyType());
    }
}
