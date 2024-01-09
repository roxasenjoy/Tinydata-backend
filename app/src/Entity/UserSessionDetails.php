<?php

namespace App\Entity;

use App\Service\UserSessionService;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Routing\Exception\InvalidParameterException;

/**
 * @ORM\Table(name="user_session_details")
 * @ORM\Entity(repositoryClass="App\Repository\UserSessionDetailsRepository")
 */
class UserSessionDetails
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserSession", inversedBy="userSessionDetails")
     * @ORM\JoinColumn(name="user_session_id")
     */
    private $userSession;

    /**
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @ORM\Column(name="type", type="string", nullable=false)
     */
    private $type;

    /**
     * @ORM\Column(name="length", type="integer", nullable=true)
     */
    private $length;


    public function __construct()
    {
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return UserSession
     */
    public function getUserSession(): UserSession
    {
        return $this->userSession;
    }

    /**
     * @param mixed $userClient
     */
    public function setUserSession(UserSession $userSession): self
    {
        $this->userSession = $userSession;
        return $this;
    }

    /**
     * @return UserClient
     */
    public function getUserClient(): UserClient
    {
        return $this->getUserSession()->getUserClient();
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getType(): String
    {
        return $this->type;
    }

    public function setType(String $type): self
    {
        if(in_array($type, UserSessionService::VALID_TYPES)){
            $this->type = $type;
            return $this;
        }
        else{
            throw new InvalidParameterException("type must be listed in UserSessionServices::VALID_TYPES");
        }
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function setLength(int $length): self
    {
        $this->length = $length;
        return $this;
    }
}