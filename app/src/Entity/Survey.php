<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SurveyRepository")
 */
class Survey
{

    const WELCOME_SURERY = 1;
    const FEED_BACK_CONTENT = 2;
    const FEED_BACK_SESSION = 3;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Query", mappedBy="survey", cascade={"persist", "remove"})
     */
    private $queries;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Requirement", mappedBy="survey", cascade={"persist", "remove"})
     */
    private $requirements;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $onLoad;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Language", inversedBy="surveys", cascade={"persist"})
     */
    private $language;

    public function __construct()
    {
        $this->queries = new ArrayCollection();
        $this->requirements = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Collection|Query[]
     */
    public function getQueries(): Collection
    {
        return $this->queries;
    }

    public function addQuery(Query $query): self
    {
        if (!$this->queries->contains($query)) {
            $this->queries[] = $query;
            $query->setSurvey($this);
        }

        return $this;
    }

    public function removeQuery(Query $query): self
    {
        if ($this->queries->contains($query)) {
            $this->queries->removeElement($query);
            // set the owning side to null (unless already changed)
            if ($query->getSurvey() === $this) {
                $query->setSurvey(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Requirement[]
     */
    public function getRequirements(): Collection
    {
        return $this->requirements;
    }

    public function addRequirement(Requirement $requirement): self
    {
        if (!$this->requirements->contains($requirement)) {
            $this->requirements[] = $requirement;
            $requirement->setSurvey($this);
        }

        return $this;
    }

    public function removeRequirement(Requirement $requirement): self
    {
        if ($this->requirements->contains($requirement)) {
            $this->requirements->removeElement($requirement);
            // set the owning side to null (unless already changed)
            if ($requirement->getSurvey() === $this) {
                $requirement->setSurvey(null);
            }
        }

        return $this;
    }

    public function getOnLoad(): ?int
    {
        return $this->onLoad;
    }

    public function setOnLoad(int $onLoad): self
    {
        $this->onLoad = $onLoad;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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
