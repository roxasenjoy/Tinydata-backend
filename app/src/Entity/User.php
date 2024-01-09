<?php

namespace App\Entity;

use App\Entity\Constants\TinyCoach;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()

 */
class User implements UserInterface
{
    const SU_USER_EMAIL='tiny@tiny-coaching.com';
    const ROLE_USER = 'ROLE_USER';
    const ROLE_USER_ADMIN = 'ROLE_USER_ADMIN';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_SU_CLIENT = 'ROLE_SU_CLIENT';
    const ROLE_TINYDATA = 'ROLE_TINYDATA';

    const STATUS_COLUMN = '@status';
    const STATUS_ENABLED = '1';
    const STATUS_DISABLED = '0';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=200, unique = true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=30,nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $resetPasswordCode;


    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $passwordRequestedAt;

    /**
     * @var array
     * @ORM\Column(type="simple_array", length=200)
     */
    private $roles = ["ROLE_USER"];

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $passwordResetToken;

    /**
     * @ORM\Column(type="string", length=100,nullable=true)
     */
    private $lastName;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\UserClient", inversedBy="user", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $userClient;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $emailRecovery;

    /**
     * @ORM\Column(type="string", length=100,nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(nullable=true)
     */
    private $moodlePlatform;

    /**
     * @ORM\Column(type="boolean",nullable=true)
     */
    private $enabled = 1;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Language", inversedBy="users")
     */
    private $language;

    /**
     * @ORM\ManyToMany(targetEntity=Company::class, inversedBy="usersInScope")
     */
    private $companiesInScope;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="usersCreated")
     * @ORM\JoinColumn(nullable=true, name="created_by", referencedColumnName="id")
     */
    private $createdBy = null;

    public function __construct()
    {
        $this->userQuickReplaies = new ArrayCollection();
        $this->companiesInScope = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getPasswordResetToken()
    {
        return $this->passwordResetToken;
    }

    /**
     * @param $passwordResetToken
     * @return $this
     */
    public function setPasswordResetToken($passwordResetToken)
    {
        $this->passwordResetToken = $passwordResetToken;

        return $this;
    }


    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone=$phone;
        return $this;
    }


    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($pwd)
    {
        $this->password = $pwd;

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * TODO refactor this and add translations en <-> fr
     */
    public function getRoleString(){
        if ($this->isSuperAdminTiny())
            return "Super Administrateur tinycoaching";
            

        if ($this->isSuperAdminClient())
            return "Super Administrateur client";
        
        if ($this->isAdminClient())
            return "Administrateur client";

        return "Utilisateur";
    }

    /**company/company/
     * Set roles
     *
     * @param array $roles
     *
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    public function isSuperAdminTiny(){
        return in_array(self::ROLE_ADMIN, $this->roles);
    }

    public function isSuperAdminClient(){
        return in_array(self::ROLE_SU_CLIENT, $this->roles);
    }

    public function isAdminClient(){
        return in_array(self::ROLE_USER_ADMIN, $this->roles);
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getInscriptionDate()
    {
        return $this->getUserClient()->getInscriptionDate();
    }

    public function setInscriptionDate($date)
    {
        $this->getUserClient()->setInscriptionDate($date);
    }

    public function getDay()
    {
        if ($this->getUserClient()->getInscriptionDate()) {
            $interval = $this->getUserClient()->getInscriptionDate()->diff(new DateTime());

            return intval($interval->format('%a')) ;
        }

        return null;
    }

    public function getTcTextDay()
    {
        $day = $this->getDay();
        if ($day >= 90) {
            return ($day%90) + 10;
        }

        return $day;
    }

    /**
     * @return Collection|UserContents[]
     */
    public function getUserContents(): Collection
    {
        return $this->getUserClient()->getUserContents();
    }

    public function addUserContent(UserContents $userContent): self
    {
        $this->getUserClient()->addUserContent($userContent);
        return $this;
    }

    public function removeUserContent(UserContents $userContent): self
    {
        $this->getUserClient()->removeUserContent($userContent);
        return $this;
    }
    public function getScoreLevel()
    {
        return intval(($this->getScore() / 1024) * 100);
    }

    public function getScore()
    {
        return $this->getUserClient()->getScore();
    }

    public function getScoreInitial(): ?int
    {
        return $this->getUserClient()->getScoreInitial();
    }
    public function getBrowser()
    {
        $devices=array();
        foreach ($this->getUserDevices() as $dv) {
            $devices[] = $dv->getBrowser();
        }

        return implode(" / ", $devices);
    }
    public function getOs()
    {
        $devices=array();
        foreach ($this->getUserDevices() as $dv) {
            $devices[] = $dv->getOs();
        }

        return implode(" / ", $devices);
    }

    /**
     * @param int $scoreInitial
     * @return User
     * setScoreInitial will be called only on create our Object (we should set score)
     */
    public function setScoreInitial(int $scoreInitial): self
    {
        $this->setScore($scoreInitial);
        $this->getUserClient()->setScoreInitial($scoreInitial);

        return $this;
    }

    /**
     * @return array
     */
    public function getLaterContents()
    {
        return $this->getUserClient()->getLaterContents();
    }

    /**
     * @param $laterContents
     * @return User
     */
    public function setLaterContents($laterContents): self
    {
        $this->getUserClient()->setLaterContents($laterContents);

        return $this;
    }

    /**
     * @return Collection|UserDevice[]
     */
    public function getUserDevices(): Collection
    {
        return $this->getUserClient()->getUserDevices();
    }

    public function addUserDevice(UserDevice $userDevice): self
    {
        $this->getUserClient()->addUserDevice($userDevice);
        return $this;
    }

    public function removeUserDevice(UserDevice $userDevice): self
    {
        $this->getUserClient()->removeUserDevice($userDevice);

        return $this;
    }

    public function getFirstConnection(): ?DateTimeInterface
    {
        return $this->getUserClient()->getFirstConnection();
    }

    public function setFirstConnection(?DateTimeInterface $first_connection): self
    {
        $this->getUserClient()->setFirstConnection($first_connection);

        return $this;
    }

    public function getLastConnection(): ?DateTimeInterface
    {
        return $this->getUserClient()->getLastConnection();
    }

    public function setLastConnection(?DateTimeInterface $lastConnection): self
    {
        $this->getUserClient()->setLastConnection($lastConnection);

        return $this;
    }

    public function addTimeLineContent(Content $timeLineContent): self
    {
        $this->getUserClient()->addTimeLineContent($timeLineContent);
        return $this;
    }

    public function removeTimeLineContent(Content $timeLineContent): self
    {
        $this->getUserClient()->removeTimeLineContent($timeLineContent);
        return $this;
    }

    public function getAttribute($attr)
    {
        return $this->getUserClient()->getAttribute($attr);
    }


    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getTimeLineSkills()
    {
        return $this->getUserClient()->getTimeLineSkills();
    }

    public function getCompany(): ?Company
    {
        return $this->getUserClient() ? $this->getUserClient()->getCompany() : null;
    }

    public function companyIsActive(): bool
    {
        return $this->getUserClient() ? $this->getUserClient()->companyIsActive() :false;
    }
    
    public function getCompanyId(){
        return $this->getCompany() ? $this->getCompany()->getId() : null;
    }

    public function getCompanyName(){
        return $this->getCompany() ? $this->getCompany()->getName() : "";
    }

    public function getMotherCompanyName(){
        return $this->getMotherCompany() ? $this->getMotherCompany()->getName() : "";
    }

    public function getMotherCompany(){
        $company = $this->getCompany();
        if($company === null){
            return null;
        }
        if($company->getMotherCompany()){
            return $company->getMotherCompany();
        }
        return $company;
    }

    public function updateCompany(Company $company)
    {
        $this->setCompany($company);
        return $company;
    }

    public function setMotherCompany(Company $newCompany){

        $company = $this->getCompany();
        
        if ($company->getMotherCompany()){
            $company->setMotherCompany($newCompany);
        }else{
            $this->setCompany($newCompany);
        }

    }

    public function getSubCompany(){
        $company = $this->getCompany();
        if($company === null){
            return null;
        }
        if($company->getParent()){
            return $company;
        }
        return null;
    }

    public function setSubCompany(?Company $company)
    {
        $this->setCompany($company);
    }

    public function getSubCompanyName(){
        return $this->getSubCompany() ? $this->getSubCompany()->getName() : "";
    }

    public function setCompany(?Company $company): self
    {
        $this->getUserClient()->setCompany($company);

        return $this;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmailRecovery(): ?string
    {
        return $this->emailRecovery;
    }

    public function setEmailRecovery(?string $emailRecovery): self
    {
        $this->emailRecovery = $emailRecovery;

        return $this;
    }

    public function getUserClient(): ?UserClient
    {
        return $this->userClient;
    }

    public function setUserClient(UserClient $userClient): self
    {
        $this->userClient = $userClient;

        return $this;
    }
    
    /**
     * @ORM\PostLoad
     */
    public function getContents()
    {
        return $this->getUserClient() ? $this->getUserClient()->getContents() : [];
    }

    public function getDevices()
    {
        return $this->getUserClient()->getDevices();
    }

    public function getFullName(): ?string
    {
        return $this->getFirstName().' '.$this->getLastName();
    }

    public function getRank(): ?int
    {
        return $this->getUserClient()->getRank();
    }

    public function setRank(int $rank): self
    {
        $this->getUserClient()->setRank($rank);

        return $this;
    }


    public function getUserLevels(): Collection
    {
        return $this->getUserClient()->getUserLevels();
    }

    public function addUserLevel(UserLevel $userLevel): self
    {
        $this->getUserClient()->addUserLevel($userLevel);
        return $this;
    }

    public function removeUserLevel(UserLevel $userLevel): self
    {
        $this->getUserClient()->removeUserLevel($userLevel);

        return $this;
    }


    public function setScore(int $score): self
    {
        $this->getUserClient()->setScore($score);

        return $this;
    }
    public function getCgu(): ?bool
    {
        return $this->getUserClient()->getCgu();
    }

    public function setCgu(bool $cgu): self
    {
        $this->getUserClient()->setCgu($cgu);
        return $this;
    }

    /**
     * @return Collection|UserQuickReply[]
     */
    public function getUserQuickReplaies(): Collection
    {
        return $this->getUserClient()->getUserQuickReplaies();
    }

    public function addUserQuickReplaie(UserQuickReply $userQuickReplaie): self
    {
        $this->getUserClient()->addUserQuickReplaie($userQuickReplaie);

        return $this;
    }

    public function removeUserQuickReplaie(UserQuickReply $userQuickReplaie): self
    {
        $this->getUserClient()->removeUserQuickReplaie($userQuickReplaie);

        return $this;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getToken()
    {
        return $this->token;
    }


    /**
     * @return mixed
     */
    public function getResetPasswordCode()
    {
        return $this->resetPasswordCode;
    }

    /**
     * @param mixed $resetPasswordCode
     */
    public function setResetPasswordCode($resetPasswordCode): void
    {
        $this->resetPasswordCode = $resetPasswordCode;
    }

    /**
     * @return mixed
     */
    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }

    /**
     * @param mixed $passwordRequestedAt
     */
    public function setPasswordRequestedAt($passwordRequestedAt): void
    {
        $this->passwordRequestedAt = $passwordRequestedAt;
    }

        /**
     * @return mixed
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param mixed $enabled
     */
    public function setEnabled($enabled): void
    {
        $this->enabled = $enabled;
    }

    public function toggleEnabled(): bool
    {
        $this->enabled = !$this->enabled;
        return $this->enabled;
    }
    public function getFunction(): ?string
    {
        return $this->userClient->getFunction();
    }

    public function setFunction(?string $function): self
    {
        $this->userClient->setFunction($function);

        return $this;
    }



    /**
     * @return bool
     */
    public function isAllowNotification(): ?bool
    {
        return $this->getUserClient()->isAllowNotification();
    }

    /**
     * @param bool $allowNotification
     * @return User
     */
    public function setAllowNotification(bool $allowNotification): self
    {
        $this->getUserClient()->setAllowNotification($allowNotification);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMoodlePlatform(): ?string
    {
        return $this->moodlePlatform;
    }

    /**
     * @param mixed $moodlePlatform
     * @return User
     */
    public function setMoodlePlatform(?string $moodlePlatform): self
    {
        $this->moodlePlatform = $moodlePlatform;

        return $this;
    }

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(Language $language): self
    {
        $this->language = $language;
        return $this;
    }

    public function getProfile(){
        return $this->getUserClient()->getProfile();
    }

    public function getPayment(){
        return $this->getUserClient()->getPayment();
    }

    public function setPayment($payment){
        $this->getUserClient()->setPayment($payment);
    }
    public function getStatus(){
        return $this->getUserClient()->getStatus();
    }
    
    public function getStatusName(){
        return $this->getUserClient()->getStatus()->getStatusName();
    }

    public function setStatus($status){
        $this->getUserClient()->setStatus($status);
    }


    public function getClassName()
    {
        return (new \ReflectionClass($this))->getShortName();
    }

    /**
     * 
     * Returns string that references translations/messages.fr.yml 
     * 
     * Examples : return "inactive" --> datatable.student.column.inactive
     * Examples : return "default_value" --> datatable.student.column.default_value
     * 
     * @return String 
     */
    public function getUserStateString(){
        return strtolower($this->getUserState());
    }

    public function getUserState(){
        if (!$this->enabled)
            return TinyCoach::USER_STATE_INACTIVE;

        $userStatus = $this->getStatus();
        $statusOnboarded = TinyCoach::USER_STATUS_ONBOARDED;

        if ($userStatus == null || strcasecmp($userStatus,$statusOnboarded) != 0){ // not onboarded 
            // if is enabled but not onboarded yet, return default value
            return TinyCoach::USER_STATE_DEFAULT;
        }
        
        return TinyCoach::USER_STATE_ACTIVE;
    }

    public function isDeletable(){
        $enabled = $this->enabled;
        if ($enabled){
            return false;
        }

        $userStatus = $this->getStatus();
        $statusOnboarded = TinyCoach::USER_STATUS_ONBOARDED;
        
        $inscriptionDate  = $this->getInscriptionDate();
        if (!$inscriptionDate){
            return (strcasecmp($userStatus,$statusOnboarded) != 0);
        }
        $currentDate = new DateTime();
        $diffdays = $currentDate->diff($inscriptionDate)->format("%a");

        return (strcasecmp($userStatus,$statusOnboarded) != 0) && $diffdays < 7;
    }

    public function getOnboardingMailDate(): ?DateTimeInterface{
        return $this->getUserClient()->getOnboardingMailDate();
    }

    public function setOnboardingMailDate(?DateTimeInterface $date) {
        $this->getUserClient()->setOnboardingMailDate($date);
    }

    /**
     * @return Collection|Company[]
     */
    public function getCompaniesInScope(): Collection
    {
        return $this->companiesInScope;
    }

    public function addCompanyInScope(Company $companyInScope): self
    {
        if (!$this->companiesInScope->contains($companyInScope)) {
            $this->companiesInScope[] = $companyInScope;
        }

        return $this;
    }

    public function removeCompanyInScope(Company $companyInScope): self
    {
        $this->companiesInScope->removeElement($companyInScope);

        return $this;
    }


    public function equals (Object $obj){
        if (!($obj instanceof User))
            return false;

        return $obj->getId() == $this->getId();
    }

    /**
     * Get the value of createdBy
     */ 
    public function getCreatedBy(): self
    {
        return $this->createdBy;
    }

    /**
     * Set the value of createdBy
     *
     * @return  self
     */ 
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function isCreatedBy(User $user): bool
    {
        if (!$this->createdBy) {
            return false;
        }

        return $this->createdBy->equals($user);
    }
}
