<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReadingRepository")
 */
class Reading
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TcText", inversedBy="readings")
     */
    private $tcText;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $valueWomen;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $valueMen;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\QueryReading", inversedBy="readings")
     */
    private $queryReading;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ReadingResponse", mappedBy="reading", cascade={"persist", "remove"})
     */
    private $responses;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $readingCode;

    public function __construct($text = null)
    {
        if ($text) {
            $this->setValue($text);
        }
    }

    public function getId()
    {
        return $this->id;
    }


    public function getTcText(): ?TcText
    {
        return $this->tcText;
    }

    public function setTcText(?TcText $tcText): self
    {
        $this->tcText = $tcText;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return Reading
     */
    public function setValue($value)
    {
        $this->value = $value ;
        $this->valueMen = $value ;
        return $this;
    }

    public function getValueWomen(): ?string
    {
        return $this->valueWomen ?? $this->valueMen;
    }

    public function getOnlyValueWomen(): ?string
    {
        return $this->valueWomen;
    }

    public function setValueWomen(string $valueWomen = null)
    {
        $this->valueWomen = $valueWomen;

        return $this;
    }

    public function getValueMen(): ?string
    {
        return $this->valueMen ?? $this->valueWomen;
    }

    public function setValueMen(string $valueMen = null)
    {
        $this->valueMen = $valueMen;

        return $this;
    }

    public function getQueryReading(): ?QueryReading
    {
        return $this->queryReading;
    }

    public function setQueryReading(?QueryReading $queryReading): self
    {
        $this->queryReading = $queryReading;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getReadingCode()
    {
        return $this->readingCode;
    }

    /**
     * @param mixed $readingCode
     * @return Reading
     */
    public function setReadingCode($readingCode)
    {
        $this->readingCode = $readingCode;

        return $this;
    }

    /**
     * @return Collection|Response[]
     */
    public function getResponses()
    {
        return $this->responses ? $this->responses : new ArrayCollection();
    }

    /**
     * @param ReadingResponse|null $response
     * @return Reading
     */
    public function addResponse(ReadingResponse $response): self
    {
        $reading_responses = (is_array($this->getResponses())) ?
            new ArrayCollection($this->getResponses()) : $this->getResponses();
        if (!$reading_responses->contains($response)) {
            $this->responses[] = $response;
        }
        return $this;
    }

    public function removeResponse(ReadingResponse $response): self
    {
        if ($this->getResponses()->contains($response) && $response->getReading() === $this) {
            $this->responses->removeElement($response);
            $response->setReading(null);
        }
        return $this;
    }
}
