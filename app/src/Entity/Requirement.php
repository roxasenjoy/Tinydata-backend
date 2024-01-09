<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RequirementRepository")
 */
class Requirement
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $day;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $engaged;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $postMedia;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Content")
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TcText",inversedBy="tcRequirements")
     */
    private $tcText;

    /**
     * @ORM\ManyToMany(targetEntity="Level")
     * @ORM\JoinTable(name="requirements_levels",
     *      joinColumns={@ORM\JoinColumn(name="requirement_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="level_id", referencedColumnName="id")}
     *      )
     */
    private $levels;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Survey", inversedBy="requirements")
     */
    private $survey;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $endSession;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $endContent;

    public function __construct()
    {
        $this->levels = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDay(): ?int
    {
        return $this->day;
    }

    public function setDay(int $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getEngaged(): ?bool
    {
        return $this->engaged;
    }

    public function setEngaged(int $engaged): self
    {
        $this->engaged = $engaged;

        return $this;
    }

    public function getPostMedia(): ?int
    {
        return $this->postMedia;
    }

    public function setPostMedia(bool $postMedia): self
    {
        $this->postMedia = $postMedia;

        return $this;
    }

    public function getContent(): ?Content
    {
        return $this->content;
    }

    public function setContent(?Content $content): self
    {
        $this->content = $content;

        return $this;
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
     * @return Collection|Level[]
     */
    public function getLevels(): Collection
    {
        return $this->levels;
    }

    public function addLevel(Level $level): self
    {
        if (!$this->levels->contains($level)) {
            $this->levels[] = $level;
        }

        return $this;
    }

    public function removeLevel(Level $level): self
    {
        if ($this->levels->contains($level)) {
            $this->levels->removeElement($level);
        }

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

    public function getEndSession(): ?bool
    {
        return $this->endSession;
    }

    public function setEndSession(?bool $endSession): self
    {
        $this->endSession = $endSession;

        return $this;
    }

    public function getEndContent(): ?bool
    {
        return $this->endContent;
    }

    public function setEndContent(?bool $endContent): self
    {
        $this->endContent = $endContent;

        return $this;
    }
}
