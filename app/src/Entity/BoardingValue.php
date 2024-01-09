<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BoardingValueRepository")
 */
class BoardingValue
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserClient",cascade={"persist"},inversedBy="boardingValues")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userClient;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BoardingAttributes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $boardingAttributes;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $value;

    public function getId()
    {
        return $this->id;
    }


    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getUser(): ?UserClient
    {
        return $this->user;
    }

    public function setUser(?UserClient $user): self
    {
        $this->userClient = $user;

        return $this;
    }

    public function getBoardingAttributes(): ?BoardingAttributes
    {
        return $this->boardingAttributes;
    }

    public function setBoardingAttributes(?BoardingAttributes $boardingAttributes): self
    {
        $this->boardingAttributes = $boardingAttributes;

        return $this;
    }
}
