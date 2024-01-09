<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SkillRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 */
class Skill
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
    private $title;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SkillLevel", mappedBy="skill")
     */
    private $skillLevels;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserLevel", mappedBy="skill", cascade={"remove"})
     *
     */
    private $userLevels;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Domain", inversedBy="skills")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $domain;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $skillOrder;

    /**
     * @ORM\Column(type="string", length=16, unique=true)
     */
    private $skillCode;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Theme", mappedBy="skill", orphanRemoval=true)
     */
    private $themes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserLevelGoals", mappedBy="skill")
     */
    private $userLevelGoals;

    /**
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $updatedBy;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    public function getId()
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->skillLevels = new ArrayCollection();
        $this->themes = new ArrayCollection();
        $this->userLevelGoals = new ArrayCollection();
        $this->themes = new ArrayCollection();
        $this->userLevels = new ArrayCollection();
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
            $skillLevel->setSkill($this);
        }

        return $this;
    }

    public function removeSkillLevel(SkillLevel $skillLevel): self
    {
        if ($this->skillLevels->contains($skillLevel)) {
            $this->skillLevels->removeElement($skillLevel);
            // set the owning side to null (unless already changed)
            if ($skillLevel->getSkill() === $this) {
                $skillLevel->setSkill(null);
            }
        }

        return $this;
    }

    public function getDomain(): ?Domain
    {
        return $this->domain;
    }

    public function setDomain(?Domain $domain): self
    {
        $this->domain = $domain;

        return $this;
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

    public function getSkillOrder(): ?int
    {
        return $this->skillOrder;
    }

    public function setSkillOrder(int $skillOrder): self
    {
        $this->skillOrder = $skillOrder;

        return $this;
    }

    public function getSkillCode(): ?string
    {
        return $this->skillCode;
    }

    public function setSkillCode(string $skillCode): self
    {
        $this->skillCode = $skillCode;

        return $this;
    }

    /**
     * @return Collection|Theme[]
     */
    public function getThemes(): Collection
    {
        return $this->themes;
    }

    public function addTheme(Theme $theme): self
    {
        if (!$this->themes->contains($theme)) {
            $this->themes[] = $theme;
            $theme->setSkill($this);
        }

        return $this;
    }

    public function removeTheme(Theme $theme): self
    {
        if ($this->themes->contains($theme)) {
            $this->themes->removeElement($theme);
            // set the owning side to null (unless already changed)
            if ($theme->getSkill() === $this) {
                $theme->setSkill(null);
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

    public function addUserLevelGoal(UserLevelGoals $userLevelGoal): self
    {
        if (!$this->userLevelGoals->contains($userLevelGoal)) {
            $this->userLevelGoals[] = $userLevelGoal;
            $userLevelGoal->setSkill($this);
        }

        return $this;
    }

    public function removeUserLevelGoal(UserLevelGoals $userLevelGoal): self
    {
        if ($this->userLevelGoals->contains($userLevelGoal)) {
            $this->userLevelGoals->removeElement($userLevelGoal);
            // set the owning side to null (unless already changed)
            if ($userLevelGoal->getSkill() === $this) {
                $userLevelGoal->setSkill(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection|UserLevel[]
     */
    public function getUserLevels(): Collection
    {
        return $this->userLevels;
    }

    public function addUserLevel(UserLevel $userLevel): self
    {
        if (!$this->userLevels->contains($userLevel)) {
            $this->userLevels[] = $userLevel;
            $userLevel->setSkill($this);
        }

        return $this;
    }

    public function removeUserLevel(UserLevel $userLevel): self
    {
        if ($this->userLevels->contains($userLevel)) {
            $this->userLevels->removeElement($userLevel);
            // set the owning side to null (unless already changed)
            if ($userLevel->getSkill() === $this) {
                $userLevel->setSkill(null);
            }
        }

        return $this;
    }


    /**
     * @return mixed
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @param mixed $deletedAt
     * @return Acquisition
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
