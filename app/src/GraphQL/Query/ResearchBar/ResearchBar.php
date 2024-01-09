<?php


namespace App\GraphQL\Query\ResearchBar;

use App\GraphQL\Type\ResearchBarType;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;

class ResearchBar extends AbstractContainerAwareField
{
    public function build(FieldConfig $config)
    {

        //Return la recherche du user
        $config->addArguments([
            'researchBar' => new StringType(),
        ]);

    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        $researchBarResolver = $this->container->get('resolver.researchbar');
        return $researchBarResolver->researchBar($args['researchBar']);
    }

    /**
     * @inheritDocobjectId
     */
    public function getType()
    {
        return new ListType(new ResearchBarType());
    }
}
