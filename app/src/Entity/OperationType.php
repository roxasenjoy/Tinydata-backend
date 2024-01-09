<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OperationTypeRepository")
 */
class OperationType
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
    private $name;

    
    public function __construct()
    {
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName():string
    {
        return $this->name;
    }


    public function __toString()
    {
        return $this->getName();
    }
}

