<?php

namespace App\GraphQL\Type;

use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\DateTimeType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQLFilesBundle\GraphQL\Type\FileType;

class UserType extends AbstractObjectType
{

    /**
     * @param ObjectTypeConfig $config
     *
     * @return mixed
     */
    public function build($config)
    {
        $config->addFields([
            'id'                    => new IntType(),
            'token'                 => new StringType(),
            'firstName'             => new StringType(),
            'lastName'              => new StringType(),
            'email'                 => new StringType(),
            'profile'               => new ProfileType(),
            'permissions'           => new ListType(new PermissionType()),
            'fullName'              => new StringType(),
            'gender'                => new IntType(),
            'phone'                 => new StringType(),
            'score'                 => new IntType(),
            'rank'                  => new StringType(),
            'cgu'                   => new BooleanType(),
            'lowsAcquisitions'      => new ListType(new StringType()),
            'fortsAcquisitions'     => new ListType(new StringType()),
            'picture'               => new StringType(),
            'countLaterContents'    => new IntType() ,
            'inscriptionDate'       => new DateTimeType(),
         // 'handicaps'             => new HandicapType(),
            'image'                 => new FileType(),
            'session'               => new ListType(new StringType()),
            'hasCompanyAnalytics'   => new BooleanType(),
            'hasTinyAnalytics'      => new BooleanType(),
            'companyName'           => new StringType(),
            'roles'                 => new ListType(new StringType()),
            'allowNotification'     => new BooleanType(),
            'company'               => new CompanyType()
         ]);
    }
}
