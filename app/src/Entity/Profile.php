<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProfileRepository")
 */
class Profile
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
    private $jobTitle;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $domain;
    /**
     * @ORM\Column(type="integer")
     */
    private $score;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company",fetch="EAGER", inversedBy="profiles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $company;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserClient", mappedBy="profile", cascade={"remove"})
     */
    private $userClients;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserLevelGoals", mappedBy="profile",cascade={"persist"})
     */
    private $userLevelGoals;

    public function __construct()
    {
        $this->userClients = new ArrayCollection();
        $this->userLevelGoals = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getJobTitle(): ?string
    {
        return $this->jobTitle;
    }

    public function setJobTitle(string $jobTitle): self
    {
        $this->jobTitle = $jobTitle;

        return $this;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @param mixed $score
     */
    public function setScore($score): void
    {
        $this->score = $score;
    }


    /**
     * @return Collection|UserClient[]
     */
    public function getUserClients(): Collection
    {
        return $this->userClients;
    }

    public function addUserClient(UserClient $userClient): self
    {
        if (!$this->userClients->contains($userClient)) {
            $this->userClients[] = $userClient;
            $userClient->setProfile($this);
        }

        return $this;
    }

    public function removeUserClient(UserClient $userClient): self
    {
        if ($this->userClients->contains($userClient)) {
            $this->userClients->removeElement($userClient);
            // set the owning side to null (unless already changed)
            if ($userClient->getProfile() === $this) {
                $userClient->setProfile(null);
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
        if (!in_array($userLevelGoal, $this->userLevelGoals->getValues())) {
            $this->userLevelGoals[] = $userLevelGoal;
            $userLevelGoal->setProfile($this);
        }
        return $this;
    }

    public function removeUserLevelGoal(UserLevelGoals $userLevelGoal): self
    {
        if ($this->userLevelGoals->contains($userLevelGoal)) {
            $this->userLevelGoals->removeElement($userLevelGoal);
            // set the owning side to null (unless already changed)
            if ($userLevelGoal->getProfile() === $this) {
                $userLevelGoal->setProfile(null);
            }
        }

        return $this;
    }
}
