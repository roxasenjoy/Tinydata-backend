<?php
namespace App\GraphQL\Query;

use App\GraphQL\Query\Company\CompaniesNames;
use App\GraphQL\Query\Company\Company;
use App\GraphQL\Query\Company\CompanyInformation;
use App\GraphQL\Query\Company\CompanyNameById;
use App\GraphQL\Query\Company\CompanyObjectifs;
use App\GraphQL\Query\Filter\Filter;
use App\GraphQL\Query\Filter\FilterFormationWithData;
use App\GraphQL\Query\Granularity\Granularities;
use App\GraphQL\Query\Granularity\GranularityAcquisitions;
use App\GraphQL\Query\Granularity\GranularityContents;
use App\GraphQL\Query\Granularity\GranularityDomains;
use App\GraphQL\Query\Granularity\GranularityMatrixes;
use App\GraphQL\Query\Granularity\GranularitySkills;
use App\GraphQL\Query\Granularity\GranularityThemes;
use App\GraphQL\Query\Graphics\GraphData;
use App\GraphQL\Query\Graphics\RawData;
use App\GraphQL\Query\Granularity\TotalGranularities;

use App\GraphQL\Query\ResearchBar\ResearchBar;
use App\GraphQL\Query\ResearchBar\ResearchUsers;

use App\GraphQL\Query\Settings\Account\Account;

use App\GraphQL\Query\User\User;
use App\GraphQL\Query\User\Users;
use App\GraphQL\Query\User\UserById;

use App\GraphQL\Query\SuperAdmin\AdminAccounts;
use App\GraphQL\Query\SuperAdmin\AdminRoles;
use App\GraphQL\Query\SuperAdmin\UserIsSuperAdmin;
use App\GraphQL\Query\SuperAdmin\UserIsSuperAdminClient;
use App\GraphQL\Query\User\TinysuiteToken;
use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

class QueryType extends AbstractObjectType
{

    /**
     * @param ObjectTypeConfig $config
     *
     * @return mixed
     */
    public function build($config)
    {
        $config->addFields(
            [
                new User(),
                new Users(),
                new UserById(),
                new TinysuiteToken(),

                new ResearchBar(),
                new ResearchUsers(),

                /* Filtres */
                new Filter(),
                new FilterFormationWithData(),

                new Company(),
                new CompanyInformation(),
                new CompanyNameById(),
                new CompanyObjectifs(),
                new CompaniesNames(),

                /* Graphique PERFORMANCE */
                new Granularities(),
                new GranularityMatrixes(),
                new GranularityDomains(),
                new GranularitySkills(),
                new GranularityThemes(),
                new GranularityAcquisitions(),
                new GranularityContents(),
                new TotalGranularities(),
                /****************************/

                /* RequestV2Service */
                new RawData(),
                new GraphData(),

                new Account(),

                new AdminAccounts(),
                new AdminRoles(),

                new UserIsSuperAdmin(),
                new UserIsSuperAdminClient()
            ]
        );
    }
}
