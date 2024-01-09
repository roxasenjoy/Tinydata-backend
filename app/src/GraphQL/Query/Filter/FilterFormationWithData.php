<?php
namespace App\GraphQL\Query\Filter;

use App\GraphQL\Type\Granularity\MatrixType;
use App\GraphQL\Type\Request\CompanyInt;
use App\GraphQL\Type\Request\MatrixInt;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class FilterFormationWithData extends AbstractContainerAwareField
{
    public function build(FieldConfig $config)
    {

        $config->addArguments([
            'companies'         => new ListType(new CompanyInt()),
            'matrixFilter'      => new ListType(new MatrixInt()),
            'userId'            => new IntType(),
            'nameFilter'        => new StringType(),
            'beginDate'         => new StringType(),
            'endDate'           => new StringType()
        ]);
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        $granularitiesResolver = $this->container->get('resolver.filter');
        return $granularitiesResolver->getFormationsWithData($args['companies'], $args['matrixFilter'], $args['userId'], $args['nameFilter'], $args['beginDate'], $args['endDate']);
    }

    /**
     * @return AbstractObjectType|AbstractType
     */
    public function getType()
    {
        return new ListType(new MatrixType());
    }
}
