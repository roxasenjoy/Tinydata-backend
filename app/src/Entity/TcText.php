<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TcTextRepository")
 */
class TcText
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Reading", mappedBy="tcText", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="tc_text_id", referencedColumnName="id")
     */
    private $readings;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TcTextCondition", mappedBy="tcText", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="tc_text_id", referencedColumnName="id")
     */
    private $conditions;


    /**
     * @ORM\OneToMany(targetEntity="Requirement", mappedBy="tcText", cascade={"persist", "remove"})
     */
    private $tcRequirements;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $category;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $hasApp;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $hasNotif;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $hasMail;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $hasSms;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isMVP = false;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Language", inversedBy="tcTexts", cascade={"persist"})
     */
    private $language;


    public function __construct()
    {
        $this->readings = new ArrayCollection();
        $this->tcRequirements = new ArrayCollection();
        $this->conditions = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection|Reading[]
     */
    public function getReadings(): Collection
    {
        return $this->readings;
    }

    /**
     * @param Reading $reading
     * @return TcText
     */
    public function addReading(Reading $reading): self
    {
        if (!$this->readings->contains($reading)) {
            $this->readings[] = $reading;
            $reading->setTcText($this);
        }

        return $this;
    }

    /**
     * @param Reading $reading
     * @return TcText
     */
    public function removeReading(Reading $reading): self
    {
        if ($this->readings->contains($reading)) {
            $this->readings->removeElement($reading);
            if ($reading->getTcText() === $this) {
                $reading->setTcText(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TcTextCondition[]
     */
    public function getConditions(): Collection
    {
        return $this->conditions;
    }

    /**
     * @param TcTextCondition $condition
     * @return TcText
     */
    public function addCondition(TcTextCondition $condition): self
    {
        if (!$this->conditions->contains($condition)) {
            $this->conditions[] = $condition;
            $condition->setTcText($this);
        }

        return $this;
    }

    /**
     * @param TcTextCondition $condition
     * @return TcText
     */
    public function removeCondition(TcTextCondition $condition): self
    {
        if ($this->readings->contains($condition)) {
            $this->readings->removeElement($condition);
            if ($condition->getTcText() === $this) {
                $condition->setTcText(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTcRequirements()
    {
        return $this->tcRequirements;
    }

    /**
     * @param mixed $tcRequirements
     */
    public function setTcRequirements($tcRequirements): void
    {
        $this->tcRequirements = $tcRequirements;
    }

    public function addTcRequirement(Requirement $requirement): void
    {
        $requirement->setTcText($this);
        $this->tcRequirements[] = $requirement;
    }

    public function removeTcRequirement($requirement): void
    {
        if ($this->getTcRequirements()->contains($requirement)) {
            $this->getTcRequirements()->remove($requirement);
        }
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param $category
     * @return TcText
     */
    public function setCategory($category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getHasApp(): ?bool
    {
        return $this->hasApp;
    }

    public function setHasApp(?bool $hasApp): self
    {
        $this->hasApp = $hasApp;

        return $this;
    }

    public function getHasNotif(): ?bool
    {
        return $this->hasNotif;
    }

    public function setHasNotif(?bool $hasNotif): self
    {
        $this->hasNotif = $hasNotif;

        return $this;
    }

    public function getHasMail(): ?bool
    {
        return $this->hasMail;
    }

    public function setHasMail(?bool $hasMail): self
    {
        $this->hasMail = $hasMail;

        return $this;
    }

    public function getHasSms(): ?bool
    {
        return $this->hasSms;
    }

    public function setHasSms(?bool $hasSms): self
    {
        $this->hasSms = $hasSms;

        return $this;
    }

    public function getIsMVP(): ?bool
    {
        return $this->isMVP;
    }

    public function setIsMVP(?bool $isMVP): self
    {
        $this->isMVP = $isMVP;

        return $this;
    }

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(Language $language): self
    {
        $this->language = $language;
        return $this;
    }
}
