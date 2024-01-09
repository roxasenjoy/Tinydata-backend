<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ValueAttributeRepository")
 */
class ValueAttribute
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ContentAttribute" , fetch="LAZY", cascade={"persist"})
     */
    private $attribute;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Content", inversedBy="valueAttribute")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $content;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $value;


    public function getId()
    {
        return $this->id;
    }

    public function getAttribute(): ?ContentAttribute
    {
        return $this->attribute;
    }

    public function setAttribute(?ContentAttribute $attribute): self
    {
        $this->attribute = $attribute;

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

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }

    public function setTimeValue($value): self
    {

        if (strpos($value, 'min')) {
            $result = explode('min', $value);
            $minute = intval($result[0]) * 60;
            $sec = $result[1] ? intval($result[1]) : 0 ;
            $this->value = strval($minute + $sec);
        } elseif (strpos($value, 'sec')) {
            $result = explode('sec', $value);
            $this->value = $result[0];
        } else {
            $this->value = $value;
        }

        return $this;
    }
}
