<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PaymentRepository")
 */
class Payment
{
    const MONTHLY = "MONTH";
    const ANNUAL  = "YEAR";

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    private $type;

    public function __construct()
    {
        $this->type = $this::MONTHLY;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType(String $type): self
    {
        $this->type = $type;
        return $this;
    }


    public function __toString()
    {
        return $this->getType();
    }
}

