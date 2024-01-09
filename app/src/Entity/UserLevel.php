<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user_has_level")
 * @ORM\Entity(repositoryClass="App\Repository\UserLevelRepository")
 */
class UserLevel
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserClient",inversedBy="userLevels" )
     * @ORM\JoinColumn(nullable=false)
     */
    private $userClient;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Level",inversedBy="userLevels")
     * @ORM\JoinColumn(nullable=false)
     */
    private $level;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Skill", inversedBy="userLevels")
     * @ORM\JoinColumn(name="skill_id", referencedColumnName="id")
     * @ORM\JoinColumn(nullable=false)
     */
    private $skill;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $upgraded;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $downgraded;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $levelUpdated;

    public function __construct($user = null, $skill = null, $level = null)
    {
        if ($user) {
            $this->setUser($user);
        }
        if ($skill) {
            $this->setSkill($skill);
        }
        if ($level) {
            $this->setLevel($level);
        }
        $this->setDate(new \DateTime());
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getUser(): ?userClient
    {
        return $this->userClient;
    }

    public function setUser(?UserClient $userClient): self
    {
        $this->userClient = $userClient;

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

    public function getSkill(): ?Skill
    {
        return $this->skill;
    }

    public function setSkill(?Skill $skill): self
    {
        $this->skill = $skill;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpgraded()
    {
        return $this->upgraded;
    }

    /**
     * @param mixed $upgraded
     */
    public function setUpgraded($upgraded): void
    {
        $this->upgraded = $upgraded;
    }

    /**
     * @return mixed
     */
    public function getDowngraded()
    {
        return $this->downgraded;
    }

    /**
     * @param mixed $downgraded
     */
    public function setDowngraded($downgraded): void
    {
        $this->downgraded = $downgraded;
    }

    /**
     * @return mixed
     */
    public function getLevelUpdated()
    {
        return $this->levelUpdated;
    }

    /**
     * @param mixed $levelUpdated
     */
    public function setLevelUpdated($levelUpdated): void
    {
        $this->levelUpdated = $levelUpdated;
    }
}
