<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LanguageRepository")
 */
class Language
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
    private $name;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $shortName;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TcText", mappedBy="language", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="language_id", referencedColumnName="id")
     */
    private $tcTexts;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ImportHistory", mappedBy="language", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="language_id", referencedColumnName="id")
     */
    private $importHistories;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="language", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="language_id", referencedColumnName="id")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Survey", mappedBy="language", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="language_id", referencedColumnName="id")
     */
    private $surveys;


    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->tcTexts = new ArrayCollection();
        $this->importHistories = new ArrayCollection();
        $this->surveys = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setShortName(string $shortName): self
    {
        $this->shortName = $shortName;

        return $this;
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @param User $user
     * @return Language
     */
    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setLanguage($this);
        }

        return $this;
    }

    /**
     * @return Collection|TcText[]
     */
    public function getTcTexts(): Collection
    {
        return $this->tcTexts;
    }

    /**
     * @param TcText $tcText
     * @return Language
     */
    public function addTcText(TcText $tcText): self
    {
        if (!$this->tcTexts->contains($tcText)) {
            $this->tcTexts[] = $tcText;
            $tcText->setLanguage($this);
        }

        return $this;
    }

    /**
     * @return Collection|ImportHistory[]
     */
    public function getImportHistories(): Collection
    {
        return $this->importHistories;
    }

    /**
     * @param ImportHistory $importHistory
     * @return Language
     */
    public function addImportHistory(ImportHistory $importHistory): self
    {
        if (!$this->importHistories->contains($importHistory)) {
            $this->importHistories[] = $importHistory;
            $importHistory->setLanguage($this);
        }

        return $this;
    }

    /**
     * @return Collection|Survey[]
     */
    public function getSurveys(): Collection
    {
        return $this->surveys;
    }

    /**
     * @param Survey $survey
     * @return Language
     */
    public function addSurvey(Survey $survey): self
    {
        if (!$this->surveys->contains($survey)) {
            $this->surveys[] = $survey;
            $survey->setLanguage($this);
        }

        return $this;
    }
}