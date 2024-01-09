<?php

namespace App\ORM\Tenant;

/**
 * Interface TenantAwareInterface
 * Created by PhpStorm.
 * User: Ines Mokni <ines.mokni@proxym-it.com;mokni.inees@gmail.com>
 * Date: 26/04/18
 */
interface TenantAwareInterface
{
    /**
     * @return TenantAwareInterface
     */
    public function getTenant();
    /**
     * @param TenantAwareInterface $tenant
     *
     * @return $this
     */
    public function setTenant($tenant);
}
