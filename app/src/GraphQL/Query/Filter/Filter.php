<?php


namespace App\GraphQL\Query\Filter;

use App\GraphQL\Type\FilterType;
use App\GraphQL\Type\Request\CompanyInt;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;

class Filter extends AbstractContainerAwareField
{
    public function build(FieldConfig $config)
    {

        //Return la recherche du user
        $config->addArguments([
            'beginDate' => new StringType(),
            'endDate' => new StringType(),
            'parcours' => new ListType(new StringType()),
            'entreprise' => new ListType(new CompanyInt())
        ]);

    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        $researchBarResolver = $this->container->get('resolver.filter');
        return $researchBarResolver
            ->filter(
                $args['beginDate'],
                $args['endDate'],
                $args['parcours'],
                $args['entreprise']
            );
    }


    public function getType()
    {
        return new ListType(new FilterType());
    }
}
