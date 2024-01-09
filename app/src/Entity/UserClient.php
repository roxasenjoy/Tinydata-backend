<?php

namespace App\Entity;

use App\Entity\Traits\UserClientTrait;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\ORM\Tenant\TenantTrait;
use DateInterval;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserClientRepository")
 */
class UserClient
{

    use TenantTrait;
    use UserClientTrait;

    const ROLE_USER = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const USER_PIX = 'USER_PIX';
    const USER_AUTOEVAL = 'USER_AUTOEVAL';

    const TIMELINE_TYPE_FORMATION_CONTENT       = 1;
    const TIMELINE_TYPE_SEMANTIC_SEARCH_CONTENT = 2;
    const TIMELINE_TYPE_LATER_CONTENT           = 3;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserContents", mappedBy="userClient", cascade={"persist", "remove"})
     */
    private $userContents;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserConnections", mappedBy="userClient", cascade={"persist", "remove"})
     */
    private $userConnections;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserSession", mappedBy="userClient", cascade={"persist", "remove"})
     */
    private $userSessions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BoardingValue", mappedBy="userClient", cascade={"remove"})
     */
    private $boardingValues;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserLevel",
     *     mappedBy="userClient", fetch="EAGER",cascade={"persist","remove"})
     */
    private $userLevels;

    /**
     * @ORM\Column(type="integer")
     */
    private $scoreInitial = 0;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $laterContents;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserDevice", mappedBy="userClient" , cascade={"remove"})
     */
    private $userDevices;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $first_connection;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastConnection;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $last2Connection;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isImported = true;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="users")
     * @ORM\JoinColumn(nullable=true)
     */
    private $company;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", mappedBy="userClient", cascade={"persist","remove"})
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     */
    private $score = 0;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Profile", inversedBy="userClients")
     * @ORM\JoinColumn(nullable=true)
     */
    private $profile;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $userType;
    

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserQuickReply", mappedBy="userClient" ,cascade={"remove"})
     */
    private $userQuickReplaies;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $notifHour;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Notification", mappedBy="userClient", orphanRemoval=true)
     */
    private $notifications;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rank;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $age;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $daltonien;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $dyslexie;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $malvoyant;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $malentendant;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $gender;

    /**
     * @ORM\OneToOne(targetEntity="Image", fetch="EAGER")
     * @ORM\JoinColumn(referencedColumnName="id",nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="integer")
     */
    private $successiveDays = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $absenceDays = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $daysConnection = 0;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $readContents;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $favContentType;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $favContentTime;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Content")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $timeLineContent;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $timeLineContentType;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $onBoardingProfile;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $beginOnBoarding;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $beginContent;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $longitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $latitude;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $rankUpdated;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $analytic1;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $analytic2;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $analytic3;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $scoreCompany;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rankCompany;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $function;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $sessionIndex;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $allowNotification = false;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $fireBaseGroupId;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $lastMatrixId;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    private $lastSemanticSearchDate;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $sessionLength;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    private $progressBar;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Payment", inversedBy="users")
     * @ORM\JoinColumn(name="payment_type",referencedColumnName="id",nullable=true)
     */
    private $payment;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Status", inversedBy="users")
     * @ORM\JoinColumn(name="status",referencedColumnName="id",nullable=true)
     */
    private $userStatus;


    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $onboardingMailDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @ORM\OneToMany(targetEntity=DomainProgression::class, mappedBy="user", orphanRemoval=true)
     */
    private $domainProgressions;

    /**
     * UserClient constructor.
     */
    public function __construct()
    {
        $this->userContents = new ArrayCollection();
        $this->userDevices = new ArrayCollection();
        $this->boardingValues = new ArrayCollection();
        $this->userLevels = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->domainProgressions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|UserContents[]
     */
    public function getUserContents(): Collection
    {
        $array =  $this->userContents->filter(function (UserContents $userContents) {
            return $userContents->getContent();
        });

        return $array;
    }

    public function addUserContent(UserContents $userContent): self
    {
        if (!$this->userContents->contains($userContent)) {
            $this->userContents[] = $userContent;
            $userContent->setUser($this);
        }

        return $this;
    }

    public function removeUserContent(UserContents $userContent): self
    {
        if ($this->userContents->contains($userContent)) {
            $this->userContents->removeElement($userContent);
            // set the owning side to null (unless already changed)
            if ($userContent->getUser() === $this) {
                $userContent->setUser(null);
            }
        }
        return $this;
    }

    public function getDevices()
    {
        $devices = array();
        foreach ($this->getUserDevices() as $dv) {
            $devices[] = str_replace("unknown", "web", $dv->getDevice());
        }

        return implode(" / ", $devices);
    }

    public function getFullName(): ?string
    {
        return $this->getUser()->getFullName();
    }

    /**
     * @return Collection|UserLevel[]
     */
    public function getUserLevels(): Collection
    {
        return $this->userLevels;
    }

    public function addUserLevel(UserLevel $userLevel): self
    {
        if (!$this->userLevels->contains($userLevel)) {
            $this->userLevels[] = $userLevel;
            $userLevel->setUser($this);
        }

        return $this;
    }

    public function removeUserLevel(UserLevel $userLevel): self
    {
        if ($this->userLevels->contains($userLevel)) {
            $this->userLevels->removeElement($userLevel);
            // set the owning side to null (unless already changed)
            if ($userLevel->getUser() === $this) {
                $userLevel->setUser(null);
            }
        }

        return $this;
    }

    public function setScore(int $score): self
    {
        $this->score = $score;

        return $this;
    }


    public function addScore(int $score): self
    {
        $this->scoreCompany += $score;
        
        return $this;
    }

    public function getScore()
    {
        return $this->score;
    }

    public function getScoreInitial(): ?int
    {
        return $this->scoreInitial;
    }

    public function setScoreInitial(int $scoreInitial): self
    {
        $this->scoreInitial = $scoreInitial;

        return $this;
    }

    public function getLaterContents()
    {
        $_laterContents = array();
        foreach ($this->getUserContents() as $userContent) {
            if ($userContent->getLater() && $userContent->getStatus() !== true) {
                $_laterContents[] = $userContent->getContent();
            }
        }

        return $_laterContents;
    }

    public function setLaterContents($laterContents): self
    {
        $this->laterContents = $laterContents;

        return $this;
    }

    /**
     * @return Collection|UserDevice[]
     */
    public function getUserDevices(): Collection
    {
        return $this->userDevices;
    }

    public function addUserDevice(UserDevice $userDevice): self
    {
        if (!$this->userDevices->contains($userDevice)) {
            $this->userDevices[] = $userDevice;
            $userDevice->setUser($this);
        }

        return $this;
    }

    public function removeUserDevice(UserDevice $userDevice): self
    {
        if ($this->userDevices->contains($userDevice)) {
            $this->userDevices->removeElement($userDevice);
            // set the owning side to null (unless already changed)
            if ($userDevice->getUser() === $this) {
                $userDevice->setUser(null);
            }
        }

        return $this;
    }

    public function getFirstConnection(): ?DateTimeInterface
    {
        return $this->first_connection;
    }

    public function setFirstConnection(?DateTimeInterface $first_connection): self
    {
        $this->first_connection = $first_connection;

        return $this;
    }

    public function getLastConnection(): ?DateTimeInterface
    {
        return $this->lastConnection;
    }

    public function getFirstName()
    {
        return ucfirst(strtolower($this->getAttribute("firstname")));
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function companyIsActive() : bool
    {
        return $this->company ? $this->company->isActive() : false;
    }
    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        // set the owning side of the relation if necessary
        if ($this !== $user->getUserClient()) {
            $user->setUserClient($this);
        }

        return $this;
    }

    /**
     * @param mixed $lastConnection
     */
    public function setLastConnection($lastConnection): void
    {
        $this->lastConnection = $lastConnection;
    }

    /**
     * @return Profile|null
     */
    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    /**
     * @param Profile|null $profile
     * @return UserClient
     */
    public function setProfile(?Profile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserType(): string
    {
        /**TODO update all users types**/
        return $this->userType ? $this->userType : self::USER_AUTOEVAL;
    }

    /**
     * @param string $userType
     * @return UserClient
     */
    public function setUserType($userType): self
    {
        $this->userType = $userType;

        return $this;
    }


    /**
     * @return Collection|UserQuickReply[]
     */
    public function getUserQuickReplaies(): Collection
    {
        return $this->userQuickReplaies;
    }

    public function addUserQuickReplaie(UserQuickReply $userQuickReplaie): self
    {
        if (!$this->userQuickReplaies->contains($userQuickReplaie)) {
            $this->userQuickReplaies[] = $userQuickReplaie;
            $userQuickReplaie->setTr($this);
        }

        return $this;
    }

    public function removeUserQuickReplaie(UserQuickReply $userQuickReplaie): self
    {
        if ($this->userQuickReplaies->contains($userQuickReplaie)) {
            $this->userQuickReplaies->removeElement($userQuickReplaie);
            // set the owning side to null (unless already changed)
            if ($userQuickReplaie->getTr() === $this) {
                $userQuickReplaie->setTr(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * @param mixed $rank
     */
    public function setRank($rank): void
    {
        $this->rank = $rank;
    }

    /**
     * @return mixed
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param mixed $age
     */
    public function setAge($age): void
    {
        $this->age = $age;
    }

    /**
     * @return mixed
     */
    public function getDaltonien()
    {
        return $this->daltonien;
    }

    /**
     * @param mixed $daltonien
     */
    public function setDaltonien($daltonien): void
    {
        $this->daltonien = $daltonien;
        if ($daltonien == true) {
            $this->malentendant = false;
            $this->dyslexie = false;
            $this->malvoyant = false;
        }
    }

    /**
     * @return mixed
     */
    public function getDyslexie()
    {
        return $this->dyslexie;
    }

    /**
     * @param mixed $dyslexie
     */
    public function setDyslexie($dyslexie): void
    {
        $this->dyslexie = $dyslexie;
        if ($dyslexie == true) {
            $this->malentendant = false;
            $this->daltonien = false;
            $this->malvoyant = false;
        }
    }

    /**
     * @return mixed
     */
    public function getMalvoyant()
    {
        return $this->malvoyant;
    }

    /**
     * @param mixed $malvoyant
     */
    public function setMalvoyant($malvoyant): void
    {
        $this->malvoyant = $malvoyant;
        if ($malvoyant == true) {
            $this->malentendant = false;
            $this->daltonien = false;
            $this->dyslexie = false;
        }
    }

    /**
     * @return mixed
     */
    public function getMalentendant()
    {
        return $this->malentendant;
    }

    /**
     * @param mixed $malentendant
     */
    public function setMalentendant($malentendant): void
    {
        $this->malentendant = $malentendant;
        if ($malentendant == true) {
            $this->daltonien = false;
            $this->dyslexie = false;
            $this->malvoyant = false;
        }
    }

    /**
     * @return mixed
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param mixed $gender
     */
    public function setGender($gender): void
    {
        $this->gender = $gender;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image): void
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getSuccessiveDays()
    {
        return $this->successiveDays;
    }

    /**
     * @param mixed $successiveDays
     */
    public function setSuccessiveDays($successiveDays): void
    {
        $this->successiveDays = $successiveDays;
    }

    /**
     * @return mixed
     */
    public function getAbsenceDays()
    {
        return $this->absenceDays;
    }

    /**
     * @param mixed $absenceDays
     */
    public function setAbsenceDays($absenceDays): void
    {
        $this->absenceDays = $absenceDays;
    }

    public function getNotifHour(): ?int
    {
        return $this->notifHour;
    }

    public function setNotifHour(?int $notifHour): self
    {
        $this->notifHour = $notifHour;

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
            // set the owning side to null (unless already changed)
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }

        return $this;
    }

    public function getFavContentType(): ?string
    {
        return $this->favContentType;
    }

    public function setFavContentType(?string $favContentType): self
    {
        $this->favContentType = $favContentType;

        return $this;
    }

    public function getFavContentTime(): ?string
    {
        return $this->favContentTime;
    }

    public function setFavContentTime(?string $favContentTime): self
    {
        $this->favContentTime = $favContentTime;

        return $this;
    }

    public function getTimeLineContent(): ?Content
    {
        return $this->timeLineContent;
    }

    public function getTimeLineContentType(): ?int
    {
        return $this->timeLineContentType;
    }

    public function setTimeLineContent(?Content $timeLineContent): self
    {
        $this->timeLineContent = $timeLineContent;
        return $this;
    }

    public function setNextContent(?Content $timeLineContent, int $timeLineContentType): self
    {
        $this->timeLineContent = $timeLineContent;
        $this->timeLineContentType = $timeLineContentType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDaysConnection()
    {
        return $this->daysConnection;
    }

    /**
     * @param mixed $daysConnection
     */
    public function setDaysConnection($daysConnection): void
    {
        $this->daysConnection = $daysConnection;
    }

    /**
     * @return mixed
     */
    public function getFirstUpgrade()
    {
        return $this->first_upgrade;
    }

    /**
     * @param mixed $first_upgrade
     */
    public function setFirstUpgrade($first_upgrade): void
    {
        $this->first_upgrade = $first_upgrade;
    }

    public function getOnBoardingProfile(): ?int
    {
        return $this->onBoardingProfile;
    }

    public function setOnBoardingProfile(?int $onBoardingProfile): self
    {
        $this->onBoardingProfile = $onBoardingProfile;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBeginOnBoarding()
    {
        return $this->beginOnBoarding;
    }

    /**
     * @param mixed $beginOnBoarding
     */
    public function setBeginOnBoarding($beginOnBoarding): void
    {
        $this->beginOnBoarding = $beginOnBoarding;
    }

    /**
     * @return mixed
     */
    public function getBeginContent()
    {
        return $this->beginContent;
    }

    /**
     * @param mixed $beginContent
     */
    public function setBeginContent($beginContent): void
    {
        $this->beginContent = $beginContent;
    }

    /**
     * @return mixed
     */
    public function getLast2Connection()
    {
        return $this->last2Connection;
    }

    /**
     * @param mixed $last2Connection
     */
    public function setLast2Connection($last2Connection): void
    {
        $this->last2Connection = $last2Connection;
    }

    /**
     * @return mixed
     */
    public function getRankUpdated()
    {
        return $this->rankUpdated;
    }

    /**
     * @param mixed $rankUpdated
     */
    public function setRankUpdated($rankUpdated): void
    {
        $this->rankUpdated = $rankUpdated;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param mixed $longitude
     */
    public function setLongitude($longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param mixed $latitude
     */
    public function setLatitude($latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @return mixed
     */
    public function getReadContents()
    {
        return $this->readContents;
    }

    /**
     * @param mixed $readContents
     */
    public function setReadContents($readContents): void
    {
        $this->readContents = $readContents;
    }

    /**
     * @return mixed
     */
    public function getAnalytic1()
    {
        return $this->analytic1;
    }

    /**
     * @param mixed $analytic1
     */
    public function setAnalytic1($analytic1): void
    {
        $this->analytic1 = $analytic1;
    }

    /**
     * @return mixed
     */
    public function getAnalytic2()
    {
        return $this->analytic2;
    }

    /**
     * @param mixed $analytic2
     */
    public function setAnalytic2($analytic2): void
    {
        $this->analytic2 = $analytic2;
    }

    /**
     * @return mixed
     */
    public function getAnalytic3()
    {
        return $this->analytic3;
    }

    /**
     * @param mixed $analytic3
     */
    public function setAnalytic3($analytic3): void
    {
        $this->analytic3 = $analytic3;
    }

    public function getScoreCompany(): ?int
    {
        return $this->scoreCompany ? $this->scoreCompany : 0;
    }

    public function setScoreCompany(?int $scoreCompany): self
    {
        $this->scoreCompany = $scoreCompany;

        return $this;
    }

    public function getRankCompany(): ?int
    {
        return $this->rankCompany;
    }

    public function setRankCompany(?int $rankCompany): self
    {
        $this->rankCompany = $rankCompany;

        return $this;
    }

    public function getFunction(): ?string
    {
        return $this->function;
    }

    public function setFunction(?string $function): self
    {
        $this->function = $function;

        return $this;
    }

    public function getSessionIndex(): ?int
    {
        return $this->sessionIndex;
    }

    public function setSessionIndex(?int $sessionIndex): self
    {
        $this->sessionIndex = $sessionIndex;

        return $this;
    }

    public function upgradeSessionIndex()
    {
        $this->sessionIndex = $this->sessionIndex ? ($this->sessionIndex + 1 ) : 2 ;
    }

    /**
     * @return bool
     */
    public function isImported(): bool
    {
        return $this->isImported;
    }

    /**
     * @param bool $isImported
     * @return UserClient
     */
    public function setIsImported(bool $isImported): self
    {
        $this->isImported = $isImported;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAllowNotification(): ?bool
    {
        return $this->allowNotification;
    }

    /**
     * @param bool $allowNotification
     * @return UserClient
     */
    public function setAllowNotification(bool $allowNotification): self
    {
        $this->allowNotification = $allowNotification;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFireBaseGroupId(): ?string
    {
        return $this->fireBaseGroupId;
    }

    /**
     * @param string|null $fireBaseGroupId
     * @return UserClient
     */
    public function setFireBaseGroupId(?string $fireBaseGroupId)
    {
        $this->fireBaseGroupId = $fireBaseGroupId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastSemanticSearchDate(): ?string
    {
        return $this->lastSemanticSearchDate;
    }

    /**
     * @param string|null $fireBaseGroupId
     * @return UserClient
     */
    public function setLastSemanticSearchDate(?string $lastSemanticSearchDate)
    {
        $this->lastSemanticSearchDate = $lastSemanticSearchDate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastMatrixId(): ?string
    {
        return $this->lastMatrixId;
    }

    /**
     * @param mixed $lastMatrixId
     * @return UserClient
     */
    public function setLastMatrixId($lastMatrixId)
    {
        $this->lastMatrixId = $lastMatrixId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSessionLength() : ?string
    {
        return $this->sessionLength;
    }

    /**
     * @param mixed $sessionLength
     * @return UserClient
     */
    public function setSessionLength($sessionLength): self
    {
        $this->sessionLength = $sessionLength;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProgressBar()
    {
        return $this->progressBar;
    }

    public function setProgressBar($progressBar) : self
    {
        $this->progressBar = $progressBar;

        return $this;
    }
    
    
    public function getPayment(){
        return $this->payment;
    }
    
    public function setPayment($payment) : self
    {
        $this->payment = $payment;

        return $this;
    }
    public function getStatus(){
        return $this->userStatus;
    }
    
    public function setStatus($status) : self
    {
        $this->userStatus = $status;

        return $this;
    }


    public function getOnboardingMailDate(): ?DateTimeInterface{
        return $this->onboardingMailDate;
    }

    public function setOnboardingMailDate(?DateTimeInterface $date) {
        $this->onboardingMailDate =  $date;
    }

    public function getEndDate(): ?DateTimeInterface{
        if ($this->company->contractIsLicenseType()){
            $nbMonths = $this->getCompany()->getLicenseDuration();
            $date = clone $this->getInscriptionDate();
            date_add($date, date_interval_create_from_date_string("$nbMonths months"));
            return $date;
        }

        return $this->company->getContract()->getDateTo();
    }

    public function setEndDate($date) {
        $this->endDate = $date;
        return $this;
    }

    /**
     * @return Collection|DomainProgression[]
     */
    public function getDomainProgressions(): Collection
    {
        return $this->domainProgressions;
    }

    public function addDomainProgression(DomainProgression $domainProgression): self
    {
        if (!$this->domainProgressions->contains($domainProgression)) {
            $this->domainProgressions[] = $domainProgression;
            $domainProgression->setUserClient($this);
        }

        return $this;
    }

    public function removeDomainProgression(DomainProgression $domainProgression): self
    {
        if ($this->domainProgressions->removeElement($domainProgression)) {
            // set the owning side to null (unless already changed)
            if ($domainProgression->getUserClient() === $this) {
                $domainProgression->setUserClient(null);
            }
        }

        return $this;
    }

    
    public function getTrainingDuration(){  
        $beginDate = $this->getInscriptionDate();
        $endDate = $this->getEndDate();
        $difference = $endDate->getTimestamp() - $beginDate->getTimestamp();
        return round($difference / (60 * 60 * 24));
    }
}
