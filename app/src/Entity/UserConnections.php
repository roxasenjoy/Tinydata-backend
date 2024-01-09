<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="unique_user_connection", columns={"user_client_id", "date"})
 *    }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\UserConnectionsRepository")
 */
class UserConnections
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserClient", inversedBy="userConnections")
     * @ORM\JoinColumn(name="user_client_id")
     */
    private $userClient;

    /**
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;


    public function __construct()
    {
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


    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date): self
    {
        $this->date = $date;

        return $this;
    }
}