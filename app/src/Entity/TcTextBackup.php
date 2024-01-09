<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TcTextConditionRepository")
 */
class TcTextBackup
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var \DateTime $importedAt
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $importedAt;

    /**
     * @var string $backupFile
     * @ORM\Column(type="string", length=255)
     */
    private $backupFile;

    /**
     * @var string $dumpFile
     * @ORM\Column(type="string", length=255)
     */
    private $dumpFile;

    /**
     * TcTextBackup constructor.
     */
    public function __construct()
    {
        $this->setImportedAt(new \DateTime());
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     * @return TcTextCondition
     */
    public function setId($id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getImportedAt(): \DateTime
    {
        return $this->importedAt;
    }

    /**
     * @param \DateTime $importedAt
     * @return TcTextBackup
     */
    public function setImportedAt(\DateTime $importedAt): self
    {
        $this->importedAt = $importedAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getBackupFile(): string
    {
        return $this->backupFile;
    }

    /**
     * @param string $backupFile
     * @return TcTextBackup
     */
    public function setBackupFile(string $backupFile): self
    {
        $this->backupFile = $backupFile;
        return $this;
    }

    /**
     * @return string
     */
    public function getDumpFile(): string
    {
        return $this->dumpFile;
    }

    /**
     * @param string $dumpFile
     * @return TcTextBackup
     */
    public function setDumpFile(string $dumpFile): self
    {
        $this->dumpFile = $dumpFile;
        return $this;
    }
}
