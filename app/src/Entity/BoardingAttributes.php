<?php

namespace App\Entity;

use App\ORM\Tenant\TenantAwareInterface;
use Doctrine\ORM\Mapping as ORM;
use App\ORM\Tenant\TenantTrait;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BoardingAttributesRepository")
 */
class BoardingAttributes implements TenantAwareInterface
{

    use TenantTrait;


    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=45)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=400)
     */
    private $question;

    /**
     * @ORM\Column(type="integer")
     */
    private $orderAttribute;


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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getOrderAttribute(): ?int
    {
        return $this->orderAttribute;
    }

    public function setOrderAttribute(string $orderAttribute): self
    {
        $this->orderAttribute = $orderAttribute;

        return $this;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }
}
