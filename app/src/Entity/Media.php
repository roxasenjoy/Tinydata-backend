<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MediaRepository")
 */
class Media
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ValueAttribute")
     */
    private $value;

    public function getId()
    {
        return $this->id;
    }

    public function getValue(): ?ValueAttribute
    {
        return $this->value;
    }

    public function setValue(?ValueAttribute $value): self
    {
        $this->value = $value;

        return $this;
    }
}
