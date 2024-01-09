<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReadingResponseRepository")
 */
class ReadingResponse
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Reading", inversedBy="responses")
     */
    private $reading;

    /**
     * @ORM\Column(type="text")
     */
    private $valueMen;

    /**
     * @ORM\Column(type="text")
     */
    private $valueWomen;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function getReading(): ?Reading
    {
        return $this->reading;
    }

    public function setReading(?Reading $reading): self
    {
        $this->reading = $reading;

        return $this;
    }

    public function getValueWomen(): ?string
    {
        return $this->valueWomen;
    }

    public function setValueWomen(string $valueWomen): self
    {
        $this->valueWomen = $valueWomen;

        return $this;
    }

    public function getValueMen(): ?string
    {
        return $this->valueMen;
    }

    public function setValueMen(string $valueMen): self
    {
        $this->valueMen = $valueMen;

        return $this;
    }
}
