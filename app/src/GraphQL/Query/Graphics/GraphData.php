<?php
namespace App\GraphQL\Query\Graphics;

use App\GraphQL\Type\Filter\StorageFilterType;
use App\GraphQL\Type\Graphics\GraphicsReturnType;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Scalar\StringType;

class GraphData extends AbstractContainerAwareField
{
    public function build(FieldConfig $config)
    {
        $config->addArguments([
            'filterType'            => new ListType(new StorageFilterType),
            'graphName'             => new StringType(),
            'specificGraphData'     => new ListType(new StringType) // Type JSON de base
        ]);
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        return $this->container->get('resolver.graphics')->getGraphData($args);
    }

    /**
     * @return AbstractObjectType|AbstractType
     */
    public function getType()
    {
        // Le return doit être un JSON qui sera utilisé dans tous les graphiques
        return new GraphicsReturnType();
    }
}
