<?php


namespace App\GraphQL\Query\ResearchBar;

use App\GraphQL\Type\UserType;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;

class ResearchUsers extends AbstractContainerAwareField
{
    public function build(FieldConfig $config)
    {

        //Return la recherche du user
        $config->addArguments([
            'value' => new StringType(),
        ]);

    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        $researchUsers = $this->container->get('resolver.researchbar');
        return $researchUsers->researchUsers($args['value']);
    }

    /**
     * @inheritDocobjectId
     */
    public function getType()
    {
        return new ListType(new UserType());
    }
}
