<?php

namespace App\Entity;

use App\Repository\AnalyticsSkillRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AnalyticsSkillRepository")
 */
class AnalyticsSkill
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserClient", inversedBy="analyticsSkills")
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Level")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE"))
     */
    private $level;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbAcquisitionsValidated;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbAcquisitionsViewed;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $listAcquisValidated;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $listAcquisInProgress;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbAcquisitionsInProgress;

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

    public function getLevel(): ?Level
    {
        return $this->level;
    }

    public function setLevel(?Level $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getNbAcquisitionsValidated(): ?int
    {
        return $this->nbAcquisitionsValidated;
    }

    public function setNbAcquisitionsValidated(int $nbAcquisitionsValidated): self
    {
        $this->nbAcquisitionsValidated = $nbAcquisitionsValidated;

        return $this;
    }

    public function getNbAcquisitionsViewed(): ?int
    {
        return $this->nbAcquisitionsViewed;
    }

    public function setNbAcquisitionsViewed(int $nbAcquisitionsViewed): self
    {
        $this->nbAcquisitionsViewed = $nbAcquisitionsViewed;

        return $this;
    }

    public function getListAcquisValidated(): ?string
    {
        return $this->listAcquisValidated;
    }

    public function setListAcquisValidated($listAcquisValidated): self
    {
        $this->listAcquisValidated = $listAcquisValidated;

        return $this;
    }

    public function addAcquisValidated(Acquisition $acquis): self
    {
        $this->removeAcquisInProgress($acquis);
        //Si la liste est vide on ajoute l'acquis dans la liste et +1 sur le nombre
        if($this->listAcquisValidated === null || $this->listAcquisValidated === ""){
            $this->listAcquisValidated = $acquis->getAcquisCode();
            $this->nbAcquisitionsValidated++;
        }
        //Si la liste n'est pas vide, on vérifie que l'acquis n'est pas déjà présent, si c'est bon on ajoute à la liste et +1
        else{
            $acquisValidated = explode(",", $this->listAcquisValidated);
            $index = array_search($acquis->getAcquisCode(), $acquisValidated);
            if($index === FALSE){
                $this->listAcquisValidated .= ",".$acquis->getAcquisCode();
                $this->nbAcquisitionsValidated++;
            }   
        }

        return $this;
    }

    public function getListAcquisInProgress(): ?string
    {
        return $this->listAcquisInProgress;
    }

    public function setListAcquisInProgress($listAcquisInProgress): self
    {
        $this->listAcquisInProgress = $listAcquisInProgress;

        return $this;
    }

    public function addAcquisInProgress(Acquisition $acquis): self
    {
        //Si la liste est vide on ajoute l'acquis dans la liste et +1 sur le nombre
        if($this->listAcquisInProgress === null || $this->listAcquisInProgress === ""){
            $this->listAcquisInProgress = $acquis->getAcquisCode();
            $this->nbAcquisitionsInProgress++;
        }
        //Si la liste n'est pas vide, on vérifie que l'acquis n'est pas déjà présent, si c'est bon on ajoute à la liste et +1
        else{
            $acquisProgress = explode(",", $this->listAcquisInProgress);
            $index = array_search($acquis->getAcquisCode(), $acquisProgress);
            if($index === FALSE){
                $this->listAcquisInProgress .= ",".$acquis->getAcquisCode();
                $this->nbAcquisitionsInProgress++;
            }   
        }

        return $this;
    }

    public function removeAcquisInProgress(Acquisition $acquis): self
    {
        $acquisProgress = explode(",", $this->listAcquisInProgress);
        $index = array_search($acquis->getAcquisCode(), $acquisProgress);
        if($index !== FALSE){
            unset($acquisProgress[$index]);
            $this->nbAcquisitionsInProgress--;
            if(sizeof($acquisProgress) === 0){
                $this->listAcquisInProgress = null;
            }
            else{
                $this->listAcquisInProgress = implode(",", $acquisProgress);
            }
        }


        return $this;
    }

    public function getNbAcquisitionsInProgress(): ?int
    {
        return $this->nbAcquisitionsInProgress;
    }

    public function setNbAcquisitionsInProgress(?int $nbAcquisitionsInProgress): self
    {
        $this->nbAcquisitionsInProgress = $nbAcquisitionsInProgress;

        return $this;
    }
}
