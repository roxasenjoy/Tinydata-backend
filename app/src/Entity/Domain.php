<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DomainRepository")
 */
class Domain
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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $domainOrder;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Skill", mappedBy="domain", orphanRemoval=true)
     */
    private $skills;

    /**
     * @ORM\Column(type="string", length=16, unique=true)
     */
    private $domainCode;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Matrix", inversedBy="domains")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $matrix;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $updatedBy;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $UpdatedAt;

    /**
     * @ORM\OneToMany(targetEntity=DomainProgression::class, mappedBy="domain", orphanRemoval=true)
     */
    private $domainProgressions;

    public function __construct()
    {
        $this->skills = new ArrayCollection();
        $this->domainProgressions = new ArrayCollection();
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

    public function getDomainOrder(): ?int
    {
        return $this->domainOrder;
    }

    public function setDomainOrder(int $domainOrder): self
    {
        $this->domainOrder = $domainOrder;

        return $this;
    }

    /**
     * @return Collection|Skill[]
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    public function addSkill(Skill $skill): self
    {
        if (!$this->skills->contains($skill)) {
            $this->skills[] = $skill;
            $skill->setDomain($this);
        }

        return $this;
    }

    public function removeSkill(Skill $skill): self
    {
        if ($this->skills->contains($skill)) {
            $this->skills->removeElement($skill);
            // set the owning side to null (unless already changed)
            if ($skill->getDomain() === $this) {
                $skill->setDomain(null);
            }
        }

        return $this;
    }

    public function getDomainCode(): ?string
    {
        return $this->domainCode;
    }

    public function setDomainCode(string $domainCode): self
    {
        $this->domainCode = $domainCode;

        return $this;
    }

    public function getMatrix(): ?Matrix
    {
        return $this->matrix;
    }

    public function setMatrix(?Matrix $matrix): self
    {
        $this->matrix = $matrix;

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
        return $this->UpdatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $UpdatedAt): self
    {
        $this->UpdatedAt = $UpdatedAt;

        return $this;
    }

    /**
     * @return Collection|DomainProgression[]
     */
    public function getDomainProgressions(): Collection
    {
        return $this->domainProgressions;
    }

    public function addDomainProgression(DomainProgression $domainProgression): self
    {
        if (!$this->domainProgressions->contains($domainProgression)) {
            $this->domainProgressions[] = $domainProgression;
            $domainProgression->setDomain($this);
        }

        return $this;
    }

    public function removeDomainProgression(DomainProgression $domainProgression): self
    {
        if ($this->domainProgressions->removeElement($domainProgression)) {
            // set the owning side to null (unless already changed)
            if ($domainProgression->getDomain() === $this) {
                $domainProgression->setDomain(null);
            }
        }

        return $this;
    }
}
