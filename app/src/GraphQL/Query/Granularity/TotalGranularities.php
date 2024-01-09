<?php
namespace App\GraphQL\Query\Granularity;

use App\GraphQL\Type\Granularity\Total;
use App\GraphQL\Type\Request\CompanyInt;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;
use Youshido\GraphQL\Config\Field\FieldConfig;

class TotalGranularities extends AbstractContainerAwareField
{
    public function build(FieldConfig $config)
    {

        $config->addArguments([
            'companies'        => new ListType(new CompanyInt()),
        ]);
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        $totalGranularitiesResolver = $this->container->get('resolver.granularities');
        return $totalGranularitiesResolver->totalGranularities($args['companies']);
    }

    /**
     * @return AbstractObjectType|AbstractType
     */
    public function getType()
    {
        return new Total();
    }
}
