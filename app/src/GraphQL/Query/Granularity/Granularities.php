<?php
namespace App\GraphQL\Query\Granularity;

use App\GraphQL\Type\Granularity\MatrixType;
use App\GraphQL\Type\Request\CompanyInt;
use App\GraphQL\Type\Request\MatrixInt;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class Granularities extends AbstractContainerAwareField
{
    public function build(FieldConfig $config)
    {

        $config->addArguments([
            'companies'         => new ListType(new CompanyInt()),
            'matrixFilter'      => new ListType(new MatrixInt()),
            'userId'            => new IntType()
        ]);
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        $granularitiesResolver = $this->container->get('resolver.granularities');
        return $granularitiesResolver->getGranularities($args['companies'], $args['matrixFilter'], $args['userId']);
    }

    /**
     * @return AbstractObjectType|AbstractType
     */
    public function getType()
    {
        return new ListType(new MatrixType());
    }
}
