<?php
namespace App\GraphQL\Query\Granularity;

use App\GraphQL\Type\Granularity\ContentType;
use App\GraphQL\Type\Request\AcquisitionInt;
use App\GraphQL\Type\Request\CompanyInt;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;
use Youshido\GraphQL\Config\Field\FieldConfig;

class GranularityContents extends AbstractContainerAwareField
{
    public function build(FieldConfig $config)
    {

        $config->addArguments([
            'beginDate'     => new StringType(),
            'endDate'       => new StringType(),
            'contentFilter' => new ListType(new StringType()),
            'acquisID'      => new AcquisitionInt(),
        ]);
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        $granularitiesResolver = $this->container->get('resolver.granularities');
        return $granularitiesResolver->getContents($args);
    }

    /**
     * @return AbstractObjectType|AbstractType
     */
    public function getType()
    {
        return new ListType(new ContentType());
    }
}
