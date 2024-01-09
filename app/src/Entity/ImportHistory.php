<?php

namespace App\Entity;

use App\Entity\Traits\ImportHistoryTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImportHistoryRepository")
 */
class ImportHistory
{

    const STATUS_DRAFT          = 1;
    const STATUS_READY_STEP1    = 2;
    const STATUS_READY_STEP2    = 3;
    const STATUS_PROD           = 4;


    const IMPORT_TYPE_TCCONTENT     = 1;
    const IMPORT_TYPE_ONBOARDING    = 2;
    const IMPORT_TYPE_PROGOALS      = 3;
    const IMPORT_TYPE_USERSTC       = 4;
    const IMPORT_TYPE_USERSPIX      = 5;
    const IMPORT_TYPE_SIMPLE_USER   = 6;

    const NUMBER_IMPORT_HISTORY_INDEX = 1;

    use ImportHistoryTrait;
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
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $usermail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $filename;

    /**
     * @ORM\Column(type="integer")
     */
    private $status = self::STATUS_DRAFT;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Merci de télécharger un fichier.")
     * @Assert\File(mimeTypes={ "text/plain", "application/octet-stream" })
     */
    private $file;


    /**
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Matrix", inversedBy="importHistories")
     */
    private $matrix;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Language", inversedBy="importHistories", cascade={"persist"})
     */
    private $language;

    public function __construct()
    {
        $now = new \DateTime();
        $this->setDate($now);
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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getUsermail(): ?string
    {
        return $this->usermail;
    }

    public function setUsermail(string $usermail): self
    {
        $this->usermail = $usermail;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }


    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
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

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function getLanguageString()
    {
        return $this->language->getName();
    }

    public function setLanguage(Language $language): self
    {
        $this->language = $language;
        return $this;
    }
}
