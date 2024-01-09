<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserQuickReplyRepository")
 */
class UserQuickReply
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserClient", inversedBy="userQuickReplaies")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userClient;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\QuickReply")
     * @ORM\JoinColumn(nullable=false)
     */
    private $quickreply;

    public function getId()
    {
        return $this->id;
    }

    public function getUser(): ?UserClient
    {
        return $this->userClient;
    }

    public function setUser(?UserClient $user): self
    {
        $this->userClient = $user;

        return $this;
    }

    public function getQuickreply(): ?QuickReply
    {
        return $this->quickreply;
    }

    public function setQuickreply(?QuickReply $quickreply): self
    {
        $this->quickreply = $quickreply;

        return $this;
    }
}
