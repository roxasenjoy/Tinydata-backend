<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ContentAttributeRepository")
 */
class ContentAttribute
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=45)
     */
    private $contentName;

    /**
     * @ORM\Column(type="string", length=45)
     */
    private $shortTitle;

    /**
     * @ORM\Column(type="string", length=45)
     */
    private $contentType;

    /**
     * @ORM\Column(type="integer")
     */
    private $orderAttribute;

    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
        return $this->getShortTitle();
    }


    public function getOrderAttribute(): ?int
    {
        return $this->orderAttribute;
    }

    public function setOrderAttribute(int $orderAttribute): self
    {
        $this->orderAttribute = $orderAttribute;

        return $this;
    }

    public function getContentName(): ?string
    {
        return $this->contentName;
    }

    public function setContentName(string $contentName): self
    {
        $this->contentName = $contentName;

        return $this;
    }

    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    public function setContentType(string $contentType): self
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getShortTitle()
    {
        return $this->shortTitle;
    }

    /**
     * @param mixed $shortTitle
     */
    public function setShortTitle($shortTitle): void
    {
        $this->shortTitle = $shortTitle;
    }
}
