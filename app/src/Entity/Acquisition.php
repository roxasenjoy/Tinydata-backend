<?php

namespace App\Entity;

use App\Exception\CustomException;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AcquisitionRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 * @ORM\HasLifecycleCallbacks()
 */
class Acquisition
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
     * @ORM\Column(type="boolean")
     */
    private $essential;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $priorityOrder;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Content", mappedBy="acquisition", cascade={"persist", "remove"})
     */
    private $tcContents;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SkillLevel", inversedBy="aquisitions", fetch="EAGER")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $skillLevel;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Theme", inversedBy="acquisitions")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE"))
     */
    private $theme;

    /**
     * @ORM\Column(type="string", length=16, unique=true)
     */
    private $acquisCode;

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


    public function __construct()
    {
        $this->tcContents = new ArrayCollection();
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

    public function getEssential(): ?bool
    {
        return $this->essential;
    }

    public function setEssential(bool $essential): self
    {
        $this->essential = $essential;

        return $this;
    }

    public function getPriorityOrder(): ?int
    {
        return $this->priorityOrder;
    }

    public function setPriorityOrder($priorityOrder): self
    {
        if(self::getMatrix()->isLinear() && !is_numeric($priorityOrder)){
            throw new CustomException('tc.flash_message.error.priority_order');
        }
        if(is_numeric($priorityOrder)){
            if(gettype($priorityOrder) === "string"){
                $this->priorityOrder = intval($priorityOrder);
            }
            elseif(gettype($priorityOrder) == "int"){
                $this->priorityOrder = $priorityOrder;    
            }
        }
        else{
            $this->priorityOrder = null;    
        }

        return $this;
    }

    public function getTheme(): ?Theme
    {
        return $this->theme;
    }

    public function setTheme(Theme $theme): self
    {
        $this->theme = $theme;

        return $this;
    }


    /**
     * @return Collection|Content[]
     */
    public function getTcContents()
    {
        return $this->tcContents;
    }

    /**
     * @return Collection|Content[]
     */
    public function getValidTcContents()
    {
        return $this->tcContents->filter(function ($tcContent) {
            if ($tcContent->getActive()) {
                return $tcContent;
            }
        });
    }

    /**
     * @param mixed $tcContents
     */
    public function setTcContents($tcContents): void
    {
        $this->tcContents = $tcContents;
    }



    public function addTcContent(Content $tcContent): self
    {
        if (!$this->tcContents->contains($tcContent)) {
            $this->tcContents[] = $tcContent;
            $tcContent->setAcquisition($this);
        }

        return $this;
    }

    public function removeTcContent(Content $tcContent): self
    {
        if ($this->tcContents->contains($tcContent)) {
            $this->tcContents->removeElement($tcContent);
            // set the owning side to null (unless already changed)
            if ($tcContent->getAcquisition() === $this) {
                $tcContent->setAcquisition(null);
            }
        }

        return $this;
    }

    public function getSkillLevel(): ?SkillLevel
    {
        return $this->skillLevel;
    }
    
    public function getSkill(): ?Skill
    {
        return $this->skillLevel->getSkill();
    }

    public function getDomain(): ?Domain
    {
        return $this->getSkill()->getDomain();
    }

    public function getMatrix(): ?Matrix
    {
        return $this->getDomain()->getMatrix();
    }

    public function setSkillLevel(?SkillLevel $skillLevel): self
    {
        $this->skillLevel = $skillLevel;

        return $this;
    }

    public function getAcquisLevel(){
        if($this->skillLevel){
            return $this->skillLevel->getLevel();
        }
        return null;
    }


    public function getAcquisCode(): ?string
    {
        return $this->acquisCode;
    }

    public function setAcquisCode(string $acquisCode): self
    {
        $this->acquisCode = $acquisCode;

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

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
