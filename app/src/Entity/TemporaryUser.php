<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("email")
 */
class TemporaryUser
{
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
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $invitedByUser;


     /**
     * @ORM\ManytoOne(targetEntity="App\Entity\Company")
     * @ORM\JoinColumn(nullable=true)
     */
    private $company;


    /**
     * @ORM\Column(name="createdAt", type="datetime", nullable=true,unique = false)
     */
    private $createdAt;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Language", inversedBy="temporary_users")
     */
    private $language;


    /**
     * @ORM\Column(name="onboardingMailDate", type="datetime", nullable=true)
     */
    private $onboardingMailDate;

    public function __construct()
    {
        $this->createdAt = new DateTime('now', new DateTimeZone('Europe/Paris'));
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getInvitedByUser(): ?User
    {
        return $this->invitedByUser;
    }

    public function setInvitedByUser(User $user): self
    {
        $this->invitedByUser = $user;
        return $this;
    }
    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): self
    {
        $this->company = $company;
        return $this;
    }

    public function getClassName()
    {
        return (new \ReflectionClass($this))->getShortName();
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

    public function getOnboardingMailDate(): ?DateTimeInterface{
        return $this->onboardingMailDate;
    }

    public function setOnboardingMailDate(?DateTimeInterface $date) {
        $this->onboardingMailDate = $date;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
