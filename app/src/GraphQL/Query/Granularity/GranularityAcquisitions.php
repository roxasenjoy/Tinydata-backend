<?php
namespace App\GraphQL\Query\Granularity;

use App\GraphQL\Type\Granularity\AcquisitionType;
use App\GraphQL\Type\Request\AcquisitionInt;
use App\GraphQL\Type\Request\CompanyInt;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;
use Youshido\GraphQL\Config\Field\FieldConfig;

class GranularityAcquisitions extends AbstractContainerAwareField
{
    public function build(FieldConfig $config)
    {

        $config->addArguments([
            'beginDate'             => new StringType(),
            'endDate'               => new StringType(),
            'acquisitionFilter'     => new ListType(new AcquisitionInt()),
            'levelFilter'           => new ListType(new StringType()),
            'themeID'               => new IntType(),
            'showUserAcquisitions'  => new BooleanType(),
        ]);
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        $granularitiesResolver = $this->container->get('resolver.granularities');
        return $granularitiesResolver->getAcquisitions($args);
    }

    /**
     * @return AbstractObjectType|AbstractType
     */
    public function getType()
    {
        return new ListType(new AcquisitionType());
    }
}
