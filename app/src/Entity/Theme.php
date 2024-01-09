<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ThemeRepository")
 */
class Theme
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Acquisition", mappedBy="theme", cascade={"persist", "remove"})
     */
    private $acquisitions;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Skill", inversedBy="themes")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE"))
     */
    private $skill;

    /**
     * @ORM\Column(type="string", length=16, unique=true)
     */
    private $themeCode;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $updatedBy;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AnalyticsSkill", mappedBy="theme", orphanRemoval=true)
     */
    private $analytics;


    public function __construct()
    {
        $this->analytics = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
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

    public function getAcquisitions(): ?string
    {
        return $this->acquisitions;
    }

    public function setAcquisitions($acquisitions): self
    {
        $this->acquisitions = $acquisitions;

        return $this;
    }

    public function addAcquisition(Acquisition $acquisition): self
    {
        if (!$this->acquisitions->contains($acquisition)) {
            $this->acquisitions[] = $acquisition;
            $acquisition->setTheme($this);
        }

        return $this;
    }

    public function getThemeCode(): ?string
    {
        return $this->themeCode;
    }

    public function setThemeCode(string $themeCode): self
    {
        $this->themeCode = $themeCode;

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

    /**
     * @return Collection|AnalyticsSkill[]
     */
    public function getAnalytics(): Collection
    {
        return $this->analytics;
    }
}
