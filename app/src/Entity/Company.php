<?php

namespace App\Entity;

use App\Constants\CompanyContracts;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use http\Exception\InvalidArgumentException;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CompanyRepository")
 */
class Company
{
    const DEFAULT_COMPANY = 'TINYCOACHING';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="children1")
     */
    private $parent1;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Company", mappedBy="parent1")
     */
    private $children1;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="children2")
     */
    private $parent2;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Company", mappedBy="parent2")
     */
    private $children2;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="children3")
     */
    private $parent3;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Company", mappedBy="parent3")
     */
    private $children3;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="children4")
     */
    private $parent4;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Company", mappedBy="parent4")
     */
    private $children4;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="children5")
     */
    private $parent5;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Company", mappedBy="parent5")
     */
    private $children5;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="children6")
     */
    private $parent6;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Company", mappedBy="parent6")
     */
    private $children6;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="children7")
     */
    private $parent7;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Company", mappedBy="parent7")
     */
    private $children7;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="children8")
     */
    private $parent8;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Company", mappedBy="parent8")
     */
    private $children8;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="children9")
     */
    private $parent9;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Company", mappedBy="parent9")
     */
    private $children9;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="children10")
     */
    private $parent10;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Company", mappedBy="parent10")
     */
    private $children10;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default": "0"})
     */
    private $companyDepth;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $campaignCode;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserClient", mappedBy="company",cascade={"remove"})
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Contract", mappedBy="companies",cascade={"persist","remove"})
     */
    private $contracts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Profile", mappedBy="company", orphanRemoval=true)
     */
    private $profiles;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDefault = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isSimplifiedMode = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isCulturePoint = false;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ImportHistory", mappedBy="company" ,fetch="EAGER")
     */
    private $importHistories;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Matrix", mappedBy="companies")
     */
    private $matrices;

    /**
     * @ORM\Column(type="datetime")
     */
    private $deletedAt;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="companiesInScope")
     */
    private $usersInScope;

    /**
     * @ORM\Column(name="license_duration", type="integer", nullable=true)
     */
    private $licenseDuration;


    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->contracts = new ArrayCollection();
        $this->profiles = new ArrayCollection();
        $this->importHistories = new ArrayCollection();
        $this->matrices = new ArrayCollection();
        $this->usersInScope = new ArrayCollection();
        $this->companyDepth = 1;
        $this->children1 = new ArrayCollection();
        $this->children2 = new ArrayCollection();
        $this->children3 = new ArrayCollection();
        $this->children4 = new ArrayCollection();
        $this->children5 = new ArrayCollection();
        $this->children6 = new ArrayCollection();
        $this->children7 = new ArrayCollection();
        $this->children8 = new ArrayCollection();
        $this->children9 = new ArrayCollection();
        $this->children10 = new ArrayCollection();

    }

    public function __toString()
    {
        return $this->getName();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getNameWithParentName(): ?string
    {
        if($this->getParent()){
            return $this->name . "  #".$this->getParentName();
        }
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCampaignCode(): ?string
    {
        return $this->campaignCode;
    }

    public function setCampaignCode(?string $campaignCode): self
    {
        $this->campaignCode = $campaignCode;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSimplifiedMode(): bool
    {
        return $this->isSimplifiedMode;
    }

    /**
     * @param bool $isSimplifiedMode
     * @return Company
     */
    public function setIsSimplifiedMode(bool $isSimplifiedMode): self
    {
        $this->isSimplifiedMode = $isSimplifiedMode;
        return  $this;
    }

    /**
     * @return bool
     */
    public function isCulturePoint(): bool
    {
        return $this->isCulturePoint;
    }

    /**
     * @param bool $isDefault
     * @return Company
     */
    public function setIsCulturePoint(bool $isCulturePoint): self
    {
        $this->isCulturePoint = $isCulturePoint;
        return  $this;
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    /**
     * @param bool $isDefault
     * @return Company
     */
    public function setIsDefault(bool $isDefault): self
    {
        $this->isDefault = $isDefault;
        return  $this;
    }

    /**
     * ACTIVE company : show in the BO and accept new user campaignCode
     * INACTIVE company : hide in the BO and reject new user campaignCode
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->deletedAt == null;
    }

    /**
     * @param DateTime $deletedAt
     * @return Company
     */
    public function setDeletedAt(DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return  $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setCompany($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getCompany() === $this) {
                $user->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * returns the children having the specified companyDepth
     * @param int $depth
     * @return Collection
     */
    public function getChildrenDepth(int $depth): Collection
    {
        if ($depth < 1 || $depth > 10) {
            throw new InvalidArgumentException("Invalid depth value, depth should be between 1 and 10");
        }
        $children = $this->{"children".$depth};
        $filteredChildren = $children->filter(function ($child) use ($depth) {
            return $child->getCompanyDepth() == $depth+1 && $child->getDeletedAt() == null;
        });

        return $filteredChildren;

    }

    /**
     * @return Collection
     */
    public function getChildren1(): Collection
    {
        return $this->children1;
    }

    /**
     * @param Collection $children1
     * @return Company
     */
    public function setChildren1(Collection $children1): Company
    {
        $this->children1 = $children1;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getChildren2(): Collection
    {
        return $this->children2;
    }

    /**
     * @param Collection $children2
     * @return Company
     */
    public function setChildren2(Collection $children2): Company
    {
        $this->children2 = $children2;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getChildren3(): Collection
    {
        return $this->children3;
    }

    /**
     * @param Collection $children3
     * @return Company
     */
    public function setChildren3(Collection $children3): Company
    {
        $this->children3 = $children3;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getChildren4(): Collection
    {
        return $this->children4;
    }

    /**
     * @param Collection $children4
     * @return Company
     */
    public function setChildren4(Collection $children4): Company
    {
        $this->children4 = $children4;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getChildren5(): Collection
    {
        return $this->children5;
    }

    /**
     * @param Collection $children5
     * @return Company
     */
    public function setChildren5(Collection $children5): Company
    {
        $this->children5 = $children5;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getChildren6(): Collection
    {
        return $this->children6;
    }

    /**
     * @param Collection $children6
     * @return Company
     */
    public function setChildren6(Collection $children6): Company
    {
        $this->children6 = $children6;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getChildren7(): Collection
    {
        return $this->children7;
    }

    /**
     * @param Collection $children7
     * @return Company
     */
    public function setChildren7(Collection $children7): Company
    {
        $this->children7 = $children7;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getChildren8(): Collection
    {
        return $this->children8;
    }

    /**
     * @param Collection $children8
     * @return Company
     */
    public function setChildren8(Collection $children8): Company
    {
        $this->children8 = $children8;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getChildren9(): Collection
    {
        return $this->children9;
    }

    /**
     * @param Collection $children9
     * @return Company
     */
    public function setChildren9(Collection $children9): Company
    {
        $this->children9 = $children9;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getChildren10(): Collection
    {
        return $this->children10;
    }

    /**
     * @param Collection $children10
     * @return Company
     */
    public function setChildren10(Collection $children10): Company
    {
        $this->children10 = $children10;
        return $this;
    }


    public function getAllChildren(): ArrayCollection
    {
        $res = new ArrayCollection();
        for ($i = 1; $i <=10; $i++){
            $res->add($this->{"children".$i});
        }

        return $res;
    }
    /**
     * Dynamically call adequate getter 
     *  @example if (companyDepth = 1) then getParent() returns null
     *  @example if (companyDepth = 2) then getParent() returns getParent1()
     *  @example if (companyDepth = 3) then getParent() returns getParent2()
     * ... and so on
     */
    public function getParent(): ?Company{
        if ($this->companyDepth == 1)
            return null;

        $depth = $this->companyDepth-1;
        $getterName = "parent".$depth;
        $getterName = str_replace('-','', $getterName);
        $getter="get".ucwords($getterName);

        return $this->$getter();
    }

    /**
     * Dynamically call adequate setter 
     *  @example if (companyDepth = 1) then setParent($company) calls setParent1($company)
     *  @example if (companyDepth = 2) then setParent($company) calls setParent2($company)
     * ... and so on
     */
    public function setParent(?Company $company): self{
        // TODO FUNCTIONAL TESTS POSSIBLE CASES FOR $company = null
        if (!$company)
            return $this;

        $depth = $company->getCompanyDepth();
        if ($depth == 1){
            $this->setParent1($company);
            $this->setCompanyDepth(2);
            return $this;
        }

        for ($d = $depth; $d >= 1; $d--){
            $setterName = "parent".$d;
            $setterName = str_replace('-','', $setterName);
            $setter="set".ucwords($setterName);
            if ($d == $depth){
                $this->$setter($company);
                continue;
            }
            $getterDepth = $d;
            $getterName = "parent".$getterDepth;
            $getterName = str_replace('-','', $getterName);
            $getter="get".ucwords($getterName);
            $this->$setter($company->$getter());
        }
        $this->setCompanyDepth($depth+1);
        return $this; 
    }

    public function removeAllParents(){
       for ($depth = 1; $depth <=10; $depth++){
           $setterName = "parent".$depth;
           $setterName = str_replace('-','', $setterName);
           $setter="set".ucwords($setterName);
           $this->$setter(null);
        }
        return $this; 
    }
    public function getParentName(): ?String{
        return $this->getParent() ? $this->getParent()->getName() : $this->getName();
    }

    public function getChildName(): ?String{
        return $this->getParent() ? $this->getName() : "-";
    }

    public function isChildCompany(){
        return $this->getParent10() !== null;
    }

    /**
     * @return Collection|Contract[]
     */
    public function getContracts(): Collection
    {
        return $this->contracts;
    }

    public function getContractDateFrom(){
        if ($this->getContract()){
            return $this->getContract()->getDateFrom();
        }
        return null;
    }

    public function getContractDateFromString()
    {
        $date = $this->getContractDateFrom();
        if($date)
            return $date->format('d-m-Y');

        return '-';
    }


    public function setContractDateFrom($date):self{
        if ($this->getContract()){
            $this->getContract()->setDatefrom($date);
        }
        return $this;
    }
    public function getContractDateTo(){
        if ($this->getContract()){
            return $this->getContract()->getDateTo();
        }
        return null;
    }

    public function getContractDateToString()
    {
        $date = $this->getContractDateTo();
        if($date)
            return $date->format('d-m-Y');

        return '-';
    }

    public function setContractDateTo($date):self{
        if ($this->getContract()){
            $this->getContract()->setDateTo($date);
        }
        return $this;
    }
    /**
     * @return Contract|null
     */
    public function getContract(): ?Contract
    {
        if(!$this->contracts->isEmpty())
            return $this->contracts->last();

        return null;
    }


    public function setContract(Contract $c): self{
        $this->contracts->clear();
        return $this->addContract($c);
    }

    public function addContract(Contract $contract): self
    {
        if (!$this->contracts->contains($contract)) {
            $this->contracts[] = $contract;
            $contract->addCompany($this);
        }

        return $this;
    }

    public function removeContract(Contract $contract): self
    {
        if ($this->contracts->contains($contract)) {
            $this->contracts->removeElement($contract);
            // set the owning side to null (unless already changed)
            if ($contract->containsCompany($this)) {
                $contract->removeCompany($this);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Profile[]
     */
    public function getProfiles(): Collection
    {
        return $this->profiles;
    }

    public function addProfile(Profile $profile): self
    {
        if (!$this->profiles->contains($profile)) {
            $this->profiles[] = $profile;
            $profile->setCompany($this);
        }

        return $this;
    }

    public function removeProfile(Profile $profile): self
    {
        if ($this->profiles->contains($profile)) {
            $this->profiles->removeElement($profile);
            // set the owning side to null (unless already changed)
            if ($profile->getCompany() === $this) {
                $profile->setCompany(null);
            }
        }

        return $this;
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
            $importHistory->setCompany($this);
        }

        return $this;
    }

    public function removeImportHistory(ImportHistory $importHistory): self
    {
        if ($this->importHistories->contains($importHistory)) {
            $this->importHistories->removeElement($importHistory);
            // set the owning side to null (unless already changed)
            if ($importHistory->getCompany() === $this) {
                $importHistory->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Matrix[]
     */
    public function getMatrices(): Collection
    {
        return $this->matrices;
    }

    public function addMatrix(Matrix $matrix): self
    {
        if (!$this->matrices->contains($matrix)) {
            $this->matrices[] = $matrix;
            $matrix->addCompany($this);
        }

        return $this;
    }

    public function removeMatrix(Matrix $matrix): self
    {
        if ($this->matrices->contains($matrix)) {
            $this->matrices->removeElement($matrix);
            $matrix->removeCompany($this);
        }

        return $this;
    }

    public function getCompanyDepth(): ?int
    {
        return $this->companyDepth;
    }

    public function setCompanyDepth(int $companyDepth): self
    {
        $this->companyDepth = $companyDepth;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function getParent1(): ?self
    {
        return $this->parent1;
    }

    public function setParent1(?self $parent1): self
    {
        $this->parent1 = $parent1;
        return $this;
    }

    public function getParent2(): ?self
    {
        return $this->parent2;
    }

    public function setParent2(?self $parent2): self
    {
        $this->parent2 = $parent2;

        return $this;
    }

    public function getParent3(): ?self
    {
        return $this->parent3;
    }

    public function setParent3(?self $parent3): self
    {
        $this->parent3 = $parent3;

        return $this;
    }

    public function getParent4(): ?self
    {
        return $this->parent4;
    }

    public function setParent4(?self $parent4): self
    {
        $this->parent4 = $parent4;

        return $this;
    }

    public function getParent5(): ?self
    {
        return $this->parent5;
    }

    public function setParent5(?self $parent5): self
    {
        $this->parent5 = $parent5;

        return $this;
    }

    public function getParent6(): ?self
    {
        return $this->parent6;
    }

    public function setParent6(?self $parent6): self
    {
        $this->parent6 = $parent6;

        return $this;
    }

    public function getParent7(): ?self
    {
        return $this->parent7;
    }

    public function setParent7(?self $parent7): self
    {
        $this->parent7 = $parent7;

        return $this;
    }

    public function getParent8(): ?self
    {
        return $this->parent8;
    }

    public function setParent8(?self $parent8): self
    {
        $this->parent8 = $parent8;

        return $this;
    }

    public function getParent9(): ?self
    {
        return $this->parent9;
    }

    public function setParent9(?self $parent9): self
    {
        $this->parent9 = $parent9;
        return $this;
    }

    public function getParent10(): ?self
    {
        return $this->parent10;
    }

    public function setParent10(?self $parent10): self
    {
        $this->parent10 = $parent10;

        return $this;
    }

    public function getParentColumn(){
        return "parent".$this->getCompanyDepth();
    }

    public function getMotherCompany(): ?self{
        if ($this->parent1){
            return $this->parent1;
        }

        return $this;
    }
    
    public function getMotherCompanyName(): ?string{
        if ($this->getMotherCompany())
            return $this->getMotherCompany()->getName();

        return $this->getName();
    }

    public function setMotherCompany(?self $motherCompany): self{

        if (!$motherCompany){
            $this->removeAllParents();
            return $this;
        }

        $this->parent1 = $motherCompany;
        return $this;
    }


    public function isSubCompany() : bool
    {
        return $this->companyDepth > 1;
    }
    public function getContractType(): ?CompanyContractType{
        if(!$this->contracts[0])
             return null;

        return $this->contracts[0]->getContractType();
    }

    /**
     * @return ContractType::__toString() (different than ContractType::getName())
     */
    public function getContractTypeString(): ?string{
        if(!$this->getContractType())
             return ' - ';

        return $this->getContractType()->__toString();
    }

    public function setContractType(?CompanyContractType $contractType): self{
        $this->contracts[0]->setContractType($contractType);
        return $this;
    }

    public function contractIsLicenseType(): bool{
        $c = $this->getContract();
        
        if ($c && $c->getContractType())
            return $c->getContractType()->getName() == CompanyContracts::TYPE_USER_LICENSE;

        return false;
    }
    
    public function removeUserInScope(User $userInScope): self
    {
        if ($this->usersInScope->removeElement($userInScope)) {
            $userInScope->removeCompanyInScope($this);
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsersInScope(): Collection
    {
        return $this->usersInScope;
    }

    public function addUserInScope(User $userInScope): self
    {
        if (!$this->usersInScope->contains($userInScope)) {
            $this->usersInScope[] = $userInScope;
            $userInScope->addCompanyInScope($this);
        }

        return $this;
    }

    public function equals($obj)
    {
        if ($obj == $this)
            return true;
        if (!($obj instanceof Company))
            return false;

        return $obj->getId() == $this->id;
    }

    /**
     * @return mixed
     */
    public function getLicenseDuration()
    {
        return $this->licenseDuration;
    }

    /**
     * @param mixed $licenseDuration
     */
    public function setLicenseDuration($licenseDuration): self
    {
        $this->licenseDuration = $licenseDuration;

        return $this;
    }
}
