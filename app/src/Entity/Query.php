<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QueryRepository")
 */
class Query
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true, name="query_condition")
     */
    private $condition;

    /**
     * @ORM\Column(type="string", nullable=true, length=45)
     */
    private $queryType;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Survey", inversedBy="queries")
     * @ORM\JoinColumn(name="survey_id", referencedColumnName="id")
     */
    private $survey;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\QuickReply", mappedBy="query", cascade={"persist", "remove"})
     */
    private $quickReplys;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\QuickReply")
     */
    private $displayConditions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\QueryReading", mappedBy="query", orphanRemoval=true,
     *      cascade={"persist", "remove"})
     */
    private $queryReadings;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\QuickReply", mappedBy="nextQuery")
     */
    private $previousReplies;


    public function __construct()
    {
        $this->quickReplys = new ArrayCollection();
        $this->displayConditions = new ArrayCollection();
        $this->queryReadings = new ArrayCollection();
        $this->previousReplies = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Get condition
     *
     * @return string
     */
    public function getCondition(): ?string
    {
        return $this->condition;
    }

    /**
     * Set condition
     *
     * @param string $condition
     * @return Query
     */
    public function setCondition(?string $condition): self
    {
        $this->condition = $condition;

        return $this;
    }

    public function getSurvey(): ?Survey
    {
        return $this->survey;
    }

    public function setSurvey(?Survey $survey): self
    {
        $this->survey = $survey;

        return $this;
    }

    /**
     * @return Collection|QuickReply[]
     */
    public function getQuickReplys(): Collection
    {
        return $this->quickReplys;
    }

    public function addQuickReply(QuickReply $quickReply): self
    {
        if (!$this->quickReplys->contains($quickReply)) {
            $this->quickReplys[] = $quickReply;
            $quickReply->setQuery($this);
        }

        return $this;
    }

    public function removeQuickReply(QuickReply $quickReply): self
    {
        if ($this->quickReplys->contains($quickReply)) {
            $this->quickReplys->removeElement($quickReply);
            // set the owning side to null (unless already changed)
            if ($quickReply->getQuery() === $this) {
                $quickReply->setQuery(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection|QuickReply[]
     */
    public function getDisplayConditions(): Collection
    {
        return $this->displayConditions;
    }

    public function addDisplayCondition(QuickReply $displayCondition): self
    {
        if (!$this->displayConditions->contains($displayCondition)) {
            $this->displayConditions[] = $displayCondition;
        }

        return $this;
    }

    public function removeDisplayCondition(QuickReply $displayCondition): self
    {
        if ($this->displayConditions->contains($displayCondition)) {
            $this->displayConditions->removeElement($displayCondition);
        }

        return $this;
    }

    /**
     * @return Collection|QueryReading[]
     */
    public function getQueryReadings(): Collection
    {
        return $this->queryReadings;
    }

    public function addQueryReading(QueryReading $queryReading): self
    {
        if (!$this->queryReadings->contains($queryReading)) {
            $this->queryReadings[] = $queryReading;
            $queryReading->setQuery($this);
        }

        return $this;
    }

    public function removeQueryReading(QueryReading $queryReading): self
    {
        if ($this->queryReadings->contains($queryReading)) {
            $this->queryReadings->removeElement($queryReading);
            // set the owning side to null (unless already changed)
            if ($queryReading->getQuery() === $this) {
                $queryReading->setQuery(null);
            }
        }

        return $this;
    }

    public function getQueryType(): ?string
    {
        return $this->queryType;
    }

    public function setQueryType(?string $queryType): self
    {
        $this->queryType = $queryType;

        return $this;
    }

    /**
     * @return Collection|QuickReply[]
     */
    public function getPreviousReplies(): Collection
    {
        return $this->previousReplies;
    }

    public function addPreviousReply(QuickReply $previousReply): self
    {
        if (!$this->previousReplies->contains($previousReply)) {
            $this->previousReplies[] = $previousReply;
            $previousReply->setNextQuery($this);
        }

        return $this;
    }

    public function removePreviousReply(QuickReply $previousReply): self
    {
        if ($this->previousReplies->contains($previousReply)) {
            $this->previousReplies->removeElement($previousReply);
            // set the owning side to null (unless already changed)
            if ($previousReply->getNextQuery() === $this) {
                $previousReply->setNextQuery(null);
            }
        }

        return $this;
    }
}
