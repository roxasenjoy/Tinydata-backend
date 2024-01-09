<?php
namespace App\GraphQL\Query\Granularity;

use App\GraphQL\Type\Granularity\ThemeType;
use App\GraphQL\Type\Request\AcquisitionInt;
use App\GraphQL\Type\Request\CompanyInt;
use App\GraphQL\Type\Request\DomainInt;
use App\GraphQL\Type\Request\MatrixInt;
use App\GraphQL\Type\Request\SkillInt;
use App\GraphQL\Type\Request\ThemeInt;
use App\GraphQL\Type\Request\UserInt;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;
use Youshido\GraphQL\Config\Field\FieldConfig;

class GranularityThemes extends AbstractContainerAwareField
{
    public function build(FieldConfig $config)
    {

        $config->addArguments([
            'beginDate'             => new StringType(),
            'endDate'               => new StringType(),
            'matrixFilter'          => new ListType(new MatrixInt()),
            'domainFilter'          => new ListType(new DomainInt()),
            'skillFilter'           => new ListType(new SkillInt()),
            'themeFilter'           => new ListType(new ThemeInt()),
            'acquisitionFilter'     => new ListType(new AcquisitionInt()),
            'levelsFilter'          => new ListType(new StringType()),
            'objectivesFilter'      => new ListType(new StringType()),
            'organisationsFilter'   => new ListType(new CompanyInt()),
            'userInformation'       => new BooleanType(),
            'skillID'               => new SkillInt(),
            "displayAllGranularity" => new BooleanType(),
            'userId'                => new UserInt(),
            'extended'              => new BooleanType(),
            "themeID"               => new ThemeInt(),
            "displayRichData"       => new BooleanType(),
        ]);
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {

        if($args['matrixFilter'] === null){
            $args["matrixFilter"] = [];
        }

        if($args['domainFilter'] === null){
            $args["domainFilter"] =  [];
        }

        if($args['skillFilter'] === null){
            $args["skillFilter"] =  [];
        }

        if($args['themeFilter'] === null){
            $args["themeFilter"] =  [];
        }

        if($args['acquisitionFilter'] === null){
            $args["acquisitionFilter"] =  [];
        }

        if($args['levelsFilter'] === null){
            $args["levelsFilter"] =  [];
        }

        if($args['objectivesFilter'] === null){
            $args["objectivesFilter"] =  [];
        }

        if($args['organisationsFilter'] === null){
            $args["organisationsFilter"] =  [];
        }

        $granularitiesResolver = $this->container->get('resolver.granularities');
        return $granularitiesResolver->getThemes($args);
    }

    /**
     * @return AbstractObjectType|AbstractType
     */
    public function getType()
    {
        return new ListType(new ThemeType());
    }
}
