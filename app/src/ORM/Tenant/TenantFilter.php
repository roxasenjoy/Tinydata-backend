<?php

namespace App\ORM\Tenant;

use Doctrine\ORM\Query\Filter\SQLFilter;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Class TenantFilter
 * Created by PhpStorm.
 * User: Ines Mokni <ines.mokni@proxym-it.com;mokni.inees@gmail.com>
 * Date: 26/04/18
 */
class TenantFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {

        if (!$targetEntity->reflClass->implementsInterface(TenantAwareInterface::class)) {
            return '';
        }
        /**tenant_id: parameter used in test during developement phase
        replace with : return $targetTableAlias.'.tenant_id = ' . $this->getParameter('tenant_id'); **/
        return '';
    }
}
