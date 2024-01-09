<?php

namespace App\Entity;

use App\Repository\AnalyticsLeitnerRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AnalyticsLeitnerRepository")
 */
class AnalyticsLeitner
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserClient", inversedBy="analyticsLeitner")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE"))
     */
    private $user_client;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Matrix")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE"))
     */
    private $matrix;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Domain")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE"))
     */
    private $domain;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Skill")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE"))
     */
    private $skill;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Theme")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE"))
     */
    private $theme;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Acquisition")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE"))
     */
    private $acquis;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Level")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE"))
     */
    private $level;

    /**
     * @ORM\Column(type="integer")
     */
    private $current_box;

    /**
     * @ORM\Column(type="integer")
     */
    private $nb_memory_done;

    /**
     * @ORM\Column(type="integer")
     */
    private $nb_memory_failed;

    /**
     * @ORM\Column(type="text")
     */
    private $memory_progression;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserClient(): ?UserClient
    {
        return $this->user_client;
    }

    public function setUserClient(?UserClient $user_client): self
    {
        $this->user_client = $user_client;

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

    public function getDomain(): ?Domain
    {
        return $this->domain;
    }

    public function setDomain(?Domain $domain): self
    {
        $this->domain = $domain;

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

    public function getTheme(): ?Theme
    {
        return $this->theme;
    }

    public function setTheme(?Theme $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    public function getAcquis(): ?Acquisition
    {
        return $this->acquis;
    }

    public function setAcquis(?Acquisition $acquis): self
    {
        $this->acquis = $acquis;

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
    
    public function getCurrentBox(): ?int
    {
        return $this->current_box;
    }

    public function setCurrentBox(?int $current_box): self
    {
        $this->current_box = $current_box;

        return $this;
    }

    public function getNbMemoryDone(): ?int
    {
        return $this->nb_memory_done;
    }

    public function setNbMemoryDone(?int $nb_memory_done): self
    {
        $this->nb_memory_done = $nb_memory_done;

        return $this;
    }

    public function getNbMemoryFailed(): ?int
    {
        return $this->nb_memory_failed;
    }

    public function setNbMemoryFailed(?int $nb_memory_failed): self
    {
        $this->nb_memory_failed = $nb_memory_failed;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMemoryProgression(): array
    {
        return json_decode($this->memory_progression);
    }

    /**
     * @param mixed $readContents
     */
    public function setMemoryProgression(?array $memory_progression): void
    {
        $this->memory_progression = json_encode($memory_progression);
    }
    
}
