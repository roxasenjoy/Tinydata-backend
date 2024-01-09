<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LevelRepository")
 */
class Level
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $label;

    /**
     * @ORM\Column(type="integer")
     */
    private $rank;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SkillLevel", mappedBy="level")
     */
    private $skillLevels;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserLevel", mappedBy="level")
     */
    private $userLevels;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserLevelGoals", mappedBy="level")
     */
    private $userLevelGoals;

    public function __construct()
    {
        $this->skillLevels = new ArrayCollection();
        $this->userLevelGoals = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getRank() . "";
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * @param mixed $rank
     * @return Level
     */
    public function setRank($rank)
    {
        $this->rank = $rank;
        return $this;
    }

    /**
     * @return Collection|SkillLevel[]
     */
    public function getSkillLevels(): Collection
    {
        return $this->skillLevels;
    }

    public function addSkillLevel(SkillLevel $skillLevel): self
    {
        if (!$this->skillLevels->contains($skillLevel)) {
            $this->skillLevels[] = $skillLevel;
            $skillLevel->setLevel($this);
        }

        return $this;
    }

    public function removeSkillLevel(SkillLevel $skillLevel): self
    {
        if ($this->skillLevels->contains($skillLevel)) {
            $this->skillLevels->removeElement($skillLevel);
            // set the owning side to null (unless already changed)
            if ($skillLevel->getLevel() === $this) {
                $skillLevel->setLevel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserLevelGoals[]
     */
    public function getUserLevelGoals(): Collection
    {
        return $this->userLevelGoals;
    }

    /**
     * @return Collection|UserLevelGoals[]
     */
    public function getUserLevels(): Collection
    {
        return $this->userLevels;
    }

    public function addUserLevelGoal(UserLevelGoals $userLevelGoal): self
    {
        if (!$this->userLevelGoals->contains($userLevelGoal)) {
            $this->userLevelGoals[] = $userLevelGoal;
            $userLevelGoal->setLevel($this);
        }

        return $this;
    }

    public function removeUserLevelGoal(UserLevelGoals $userLevelGoal): self
    {
        if ($this->userLevelGoals->contains($userLevelGoal)) {
            $this->userLevelGoals->removeElement($userLevelGoal);
            // set the owning side to null (unless already changed)
            if ($userLevelGoal->getLevel() === $this) {
                $userLevelGoal->setLevel(null);
            }
        }

        return $this;
    }
}
