<?php

namespace App\ORM\Tenant;

/**
 * Trait TenantTrait
 * Created by PhpStorm.
 * User: Ines Mokni <ines.mokni@proxym-it.com;mokni.inees@gmail.com>
 * Date: 26/04/18
 */
trait TenantTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumn(name="tenant_id", referencedColumnName="id")
     */
    protected $tenant;
    /**
     * @return TenantAwareInterface
     */
    public function getTenant()
    {
        return $this->tenant;
    }
    /**
     * @param TenantAwareInterface $tenant
     *
     * @return $this
     */
    public function setTenant($tenant)
    {
        $this->tenant = $tenant;
        return $this;
    }
}
