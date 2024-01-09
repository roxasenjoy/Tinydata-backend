<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StatusRepository")
 */
class Status
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    private $statusName;
    
    public function __construct()
    {
    }

    public function getId()
    {
        return $this->id;
    }

    public function getStatusName():string
    {
        return $this->statusName;
    }

    public function setStatus(String $statusName): self
    {
        $this->statusName = $statusName;
        return $this;
    }

    public function __toString()
    {
        return $this->getStatusName();
    }
}

