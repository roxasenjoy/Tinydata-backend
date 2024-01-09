<?php

namespace App\GraphQL\Mutation\User;

use App\GraphQL\Type\UserDeviceType;
use App\Resolver\DayConnecetionResolver;
use App\Service\UserService;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\FloatType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;

class DayConnection extends AbstractContainerAwareField
{
    public function build(FieldConfig $config)
    {
        $config->addArguments(
            [
                'location' => new StringType(),
                'latitude' => new FloatType(),
                'longitude' => new FloatType(),
                'browser' => new StringType(),
                'browser_version' => new StringType(),
                'device' => new StringType(),
                'os' => new StringType(),
                'os_version' => new StringType(),
                'userAgent' => new StringType(),
                'firebaseToken' => new StringType(),
                'context' => new StringType()
            ]
        );
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {

        if(!array_key_exists('context', $args)){
            $args['context'] = 'Tinydata';
        }
        /** @var DayConnecetionResolver $connection_days_resolver */
        $token_resolver = $this->container->get('resolver.token');

        return $token_resolver->dayConnection($args);
    }

    /**
     * Type of retun
     *
     * @return AbstractObjectType|AbstractType
     */
    public function getType()
    {
        return new BooleanType();
    }
}
