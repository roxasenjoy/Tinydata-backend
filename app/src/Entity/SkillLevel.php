<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SkillLevelRepository")
 */
class SkillLevel
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Skill", inversedBy="skillLevels")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $skill;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Level", inversedBy="skillLevels")
     * @ORM\JoinColumn(nullable=false)
     */
    private $level;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Acquisition", mappedBy="skillLevel", fetch="EAGER")
     */
    private $aquisitions;


    public function __construct()
    {
        $this->aquisitions = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->skill->getSkillCode() . " - Niv " . $this->level->getLabel();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getSkill(): ?Skill
    {
        return $this->skill;
    }

    public function setSkill(?Skill $skill): self
    {
        $this->skill = $skill;

        return $this;
    }

    public function getLevel(): ?Level
    {
        return $this->level;
    }

    public function setLevel(?Level $level): self
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return Collection|Acquisition[]
     */
    public function getAquisitions(): Collection
    {
        return $this->aquisitions;
    }

    public function addAquisition(Acquisition $aquisition): self
    {
        if (!$this->aquisitions->contains($aquisition)) {
            $this->aquisitions[] = $aquisition;
            $aquisition->setSkillLevel($this);
        }

        return $this;
    }

    public function removeAquisition(Acquisition $aquisition): self
    {
        if ($this->aquisitions->contains($aquisition)) {
            $this->aquisitions->removeElement($aquisition);
            // set the owning side to null (unless already changed)
            if ($aquisition->getSkillLevel() === $this) {
                $aquisition->setSkillLevel(null);
            }
        }

        return $this;
    }
}
