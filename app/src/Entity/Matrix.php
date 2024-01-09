<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MatrixRepository")
 */
class Matrix
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isLinear;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Domain", mappedBy="matrix", cascade={"remove"})
     */
    private $domains;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Company", inversedBy="matrices")
     */
    private $companies;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ImportHistory", mappedBy="matrix", cascade={"remove"})
     */
    private $importHistories;

    /**
     * @ORM\Column(type="string",nullable=true,length=255)
     */
    private $name;


    public function __construct()
    {
        $this->domains = new ArrayCollection();
        $this->companies = new ArrayCollection();
        $this->importHistories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection|Domain[]
     */
    public function getDomains(): Collection
    {
        return $this->domains;
    }

    public function addDomain(Domain $domain): self
    {
        if (!$this->domains->contains($domain)) {
            $this->domains[] = $domain;
            $domain->setMatrix($this);
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    public function isLinear(): ?bool
    {
        return $this->isLinear;
    }

    public function setIsLinear($linear): self
    {
        $this->isLinear = $linear;

        return $this;
    }


    public function removeDomain(Domain $domain): self
    {
        if ($this->domains->contains($domain)) {
            $this->domains->removeElement($domain);
            // set the owning side to null (unless already changed)
            if ($domain->getMatrix() === $this) {
                $domain->setMatrix(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Company[]
     */
    public function getCompanies(): Collection
    {
        return $this->companies;
    }

    /**
     * @return Collection|Company[]
     */
    public function getActiveCompanies(): Collection
    {
        return $this->companies->filter(function($company){
            return $company->isActive();
        });
    }

    public function hasCompany(Company $company): bool{
        return $this->companies->contains($company);
    }
    /**
     * @param $companies
     * @return Matrix
     */
    public function setCompanies($companies): self
    {
        $this->companies = new ArrayCollection($companies);

        return $this;
    }

    public function addCompany(Company $company): self
    {
        if (!$this->companies->contains($company)) {
            $this->companies[] = $company;
        }

        return $this;
    }

    public function removeCompany(Company $company): self
    {
        if ($this->companies->contains($company)) {
            $this->companies->removeElement($company);
        }

        return $this;
    }

    public function getCompaniesAsString()
    {
        $titles=[];
        foreach ($this->getCompanies() as $company) {
            if($company->isActive()){
                $titles[] = $company->getName();
            }
        }
        return implode(", ", $titles);
    }

    /**
     * @return Collection|ImportHistory[]
     */
    public function getImportHistories(): Collection
    {
        return $this->importHistories;
    }

    public function addImportHistory(ImportHistory $importHistory): self
    {
        if (!$this->importHistories->contains($importHistory)) {
            $this->importHistories[] = $importHistory;
            $importHistory->setMatrix($this);
        }

        return $this;
    }

    public function removeImportHistory(ImportHistory $importHistory): self
    {
        if ($this->importHistories->contains($importHistory)) {
            $this->importHistories->removeElement($importHistory);
            // set the owning side to null (unless already changed)
            if ($importHistory->getMatrix() === $this) {
                $importHistory->setMatrix(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return "".$this->id;
    }
}
