<?php
namespace App\GraphQL\Mutation\Token;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;

class ForgotPassword extends AbstractContainerAwareField
{
    public function build(FieldConfig $config)
    {
        try {
            $config->addArguments(
                [
                    'email' => new NonNullType(new StringType()),
                ]
            );
        } catch (ConfigurationException $e) {
        }
    }
    public function resolve($value, array $args, ResolveInfo $info)
    {
        return $this->container->get('resolver.token')->forgotPassword(trim($args['email']));
    }
    /**
     * @return AbstractObjectType|AbstractType
     */
    public function getType()
    {
        return new BooleanType();
    }
}
