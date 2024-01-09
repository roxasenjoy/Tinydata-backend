<?php

namespace App\Entity;

use App\Repository\DomainProgressionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DomainProgressionRepository::class)
 */
class DomainProgression
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Domain::class, inversedBy="domainProgressions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $domain;

    /**
     * @ORM\ManyToOne(targetEntity=UserClient::class, inversedBy="domainProgressions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userClient;

    /**
     * @ORM\ManyToOne(targetEntity=Acquisition::class, inversedBy="domainProgressions")
     */
    private $lastAcquisitionValidated;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUserClient(): ?UserClient
    {
        return $this->userClient;
    }

    public function setUserClient(?UserClient $userClient): self
    {
        $this->userClient = $userClient;

        return $this;
    }

    public function getLastAcquisitionValidated(): ?Acquisition
    {
        return $this->lastAcquisitionValidated;
    }

    public function setLastAcquisitionValidated(?Acquisition $lastAcquisitionValidated): self
    {
        $this->lastAcquisitionValidated = $lastAcquisitionValidated;

        return $this;
    }
}
