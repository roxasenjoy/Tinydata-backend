<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ContractRepository")
 */
class Contract
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date",nullable=true)
     */
    private $dateFrom;

    /**
     * @ORM\Column(type="date",nullable=true)
     */
    private $dateTo;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $current = true;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Company",inversedBy="contracts")
     */
    private $companies;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CompanyContractType")
     * @ORM\JoinColumn(
     * name="contract_type",
     * referencedColumnName="id",
     * nullable=false,
     * columnDefinition="INT DEFAULT 1")
     */
    private $contractType;


    public function __construct(){
        $this->companies = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDateFrom(): ?\DateTimeInterface
    {
        return $this->dateFrom;
    }

    public function setDateFrom(?\DateTimeInterface $dateFrom): self
    {
        $this->dateFrom = $dateFrom;
        return $this;
    }

    public function getDateTo(): ?\DateTimeInterface
    {
        return $this->dateTo;
    }

    public function setDateTo(?\DateTimeInterface $dateTo): self
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    public function getCurrent(): ?bool
    {
        return $this->current;
    }

    public function setCurrent(?bool $current): self
    {
        $this->current = $current;

        return $this;
    }


    public function getAllCompanies(){
        return $this->companies;
    }

    public function getCompany(): ?Company
    {
        return $this->companies[0];
    }

    public function containsCompany(Company $company): bool{
        return $this->companies->exists(function($k,$c) use ($company){
            return $company->getId() == $c->getId();
        });
    }
    public function addCompany(Company $company): self{

        $this->companies->add($company);
        return $this;
    }


    public function removeCompany(Company $company): self{
        $this->companies->removeElement($company);
        return $this;
    }

    public function setCompanies($companies): self
    {
        $this->companies = $companies;

        return $this;
    }

    public function getContractType(): ?CompanyContractType{
        return $this->contractType;
    }

    public function setContractType(?CompanyContractType $contractType): self{
        $this->contractType = $contractType;
        return $this;
    }

}
