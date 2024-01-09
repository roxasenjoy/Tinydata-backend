<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserContentsRepository")
 */
class UserContents
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserClient", inversedBy="userContents")
     */
    private $userClient;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Content", fetch="EAGER",inversedBy="userContents", cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $content;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $status =  null;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Response")
     */
    private $responses;

    /**
     * @ORM\Column(type="datetime",  nullable=true)
     */
    private $later;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $feedback;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $contribution;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $sessionIndex;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAlreadyValidated;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $timeLineContentType;


    public function __construct()
    {
        $this->responses = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUser(): ?UserClient
    {
        return $this->getUserClient();
    }

    public function setUser(?User $user): self
    {
        $this->setUserClient($user->getUserClient());

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserClient()
    {
        return $this->userClient;
    }

    /**
     * @param mixed $userClient
     */
    public function setUserClient($userClient): void
    {
        $this->userClient = $userClient;
    }



    public function getContent(): ?Content
    {
        return $this->content;
    }

    public function setContent(?Content $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus($status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getTimeLineContentType(): ?int
    {
        return $this->timeLineContentType;
    }

    public function setTimeLineContentType($timeLineContentType): self
    {
        $this->timeLineContentType = $timeLineContentType;
        return $this;
    }

    /**
     * @return Collection|Response[]
     */
    public function getResponses(): Collection
    {
        return $this->responses;
    }

    public function addResponse(Response $response): self
    {
        if (!$this->responses->contains($response)) {
            $this->responses[] = $response;
        }

        return $this;
    }

    public function removeResponse(Response $response): self
    {
        if ($this->responses->contains($response)) {
            $this->responses->removeElement($response);
        }

        return $this;
    }

    public function getLater(): ?\DateTimeInterface
    {
        return $this->later;
    }

    public function setLater(\DateTimeInterface $later = null): self
    {
        $this->later = $later;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFeedback()
    {
        return $this->feedback;
    }

    /**
     * @param mixed $feedback
     */
    public function setFeedback($feedback): void
    {
        $this->feedback = $feedback;
    }

    /**
     * @return mixed
     */
    public function getContribution()
    {
        return $this->contribution;
    }

    /**
     * @param mixed $contribution
     */
    public function setContribution($contribution): void
    {
        $this->contribution = $contribution;
    }

    public function getSessionIndex(): ?int
    {
        return $this->sessionIndex;
    }

    public function setSessionIndex(?int $sessionIndex): self
    {
        $this->sessionIndex = $sessionIndex;

        return $this;
    }

    public function getIsAlreadyValidated(): ?bool
    {
        return $this->isAlreadyValidated;
    }

    public function setIsAlreadyValidated(bool $isAlreadyValidated): self
    {
        $this->isAlreadyValidated = $isAlreadyValidated;

        return $this;
    }
}
