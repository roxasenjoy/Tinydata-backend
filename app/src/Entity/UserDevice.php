<?php

namespace App\Entity;

use App\Entity\Traits\TimeTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserDeviceRepository")
 */
class UserDevice
{

    const CANAL_ANDROID = "Android";
    const CANAL_PWA = "PWA";
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $browser;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $browser_version;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $device;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $os;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $os_version;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $userAgent;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    private $firebaseToken;

    /**
     * @ORM\Column(type="boolean")
     */
    private $subscribed = false;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserClient", inversedBy="userDevices")
     */
    private $userClient;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $canal;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $conversationId;

    use TimeTrait;

    public function __construct($deviceType = null)
    {
        if ($deviceType) {
            $this->browser = $deviceType->browser;
            $this->browser_version = $deviceType->browser_version;
            $this->device = $deviceType->device;
            $this->os = $deviceType->os;
            $this->os_version = $deviceType->os_version;
            $this->userAgent = $deviceType->userAgent;
            $this->firebaseToken = $deviceType->firebaseToken;
            $this->canal = $deviceType->canal;
            $this->conversationId = $deviceType->conversationId;
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getBrowser(): ?string
    {
        return $this->browser;
    }

    public function setBrowser(?string $browser): self
    {
        $this->browser = $browser;

        return $this;
    }

    public function getBrowserVersion(): ?string
    {
        return $this->browser_version;
    }

    public function setBrowserVersion(?string $browser_version): self
    {
        $this->browser_version = $browser_version;

        return $this;
    }

    public function getDevice(): ?string
    {
        return $this->device;
    }

    public function setDevice(?string $device): self
    {
        $this->device = $device;

        return $this;
    }

    public function getOs(): ?string
    {
        return $this->os;
    }

    public function setOs(?string $os): self
    {
        $this->os = $os;

        return $this;
    }

    public function getOsVersion(): ?string
    {
        return $this->os_version;
    }

    public function setOsVersion(?string $os_version): self
    {
        $this->os_version = $os_version;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(?string $userAgent): self
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    public function getUser(): ?UserClient
    {
        return $this->userClient;
    }

    public function setUser(?UserClient $userClient): self
    {
        $this->userClient = $userClient;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirebaseToken(): ?string
    {
        return $this->firebaseToken;
    }

    /**
     * @param mixed $firebaseToken
     * @return UserDevice
     */
    public function setFirebaseToken($firebaseToken): self
    {
        $this->firebaseToken = $firebaseToken;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSubscribed(): ?bool
    {
        return $this->subscribed;
    }

    /**
     * @param bool $subscribed
     * @return UserDevice
     */
    public function setSubscribed(bool $subscribed): self
    {
        $this->subscribed = $subscribed;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCanal()
    {
        return $this->canal;
    }

    /**
     * @param mixed $canal
     * @return UserDevice
     */
    public function setCanal($canal): UserDevice
    {
        $this->canal = $canal;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConversationId()
    {
        return $this->conversationId;
    }

    /**
     * @param mixed $conversationId
     * @return UserDevice
     */
    public function setConversationId($conversationId): UserDevice
    {
        $this->conversationId = $conversationId;
        return $this;
    }
}
