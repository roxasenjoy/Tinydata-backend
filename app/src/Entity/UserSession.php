<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="user_session")
 * @ORM\Entity(repositoryClass="App\Repository\UserSessionRepository")
 */
class UserSession
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserClient", inversedBy="userSessions")
     * @ORM\JoinColumn(name="user_client_id")
     */
    private $userClient;

    /**
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @ORM\Column(name="session_length", type="integer", nullable=false)
     */
    private $sessionLength;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserSessionDetails", mappedBy="userSession", cascade={"persist","remove"})
     */
    private $userSessionDetails;


    public function __construct()
    {
        $this->userSessionDetails = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUserClient()
    {
        return $this->userClient;
    }

    /**
     * @param mixed $userClient
     */
    public function setUserClient($userClient): self
    {
        $this->userClient = $userClient;
        return $this;
    }


    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return int
     */
    public function getSessionLength(): int
    {
        return $this->sessionLength;
    }

    /**
     * @param int $sessionLength
     */
    public function setSessionLength(int $sessionLength): self
    {
        $this->sessionLength = $sessionLength;
        return $this;
    }

    /**
     * @param int $sessionLength
     */
    public function addSessionLength(int $sessionLength): self
    {
        $this->sessionLength += $sessionLength;
        return $this;
    }

    /**
     * @return Collection|UserSessionDetails[]
     */
    public function getUserSessionDetails(): Collection
    {
        return $this->userSessionDetails;
    }
}