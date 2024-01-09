<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RefreshContentRepository")
 */
class RefreshContent
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserClient")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $userClient;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Content")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $content;

    public function __construct(UserClient $userClient, Content $content)
    {
        $this->userClient = $userClient;
        $this->content = $content;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getContent(): ?Content
    {
        return $this->content;
    }

    public function setContent(?Content $content): self
    {
        $this->content = $content;

        return $this;
    }
}
