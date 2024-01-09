<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuickReplyRepository")
 * @ORM\Table(options={"collatation"="utf8mb4_unicode_ci"})
 */
class QuickReply
{


    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Query", inversedBy="quickReplys")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $query;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $value;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $valueMen;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $valueWomen;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\QueryReading", inversedBy="quickReplies")
     * @ORM\JoinColumn(nullable=false)
     */
    private $queryReading;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Query", inversedBy="previousReplies")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $nextQuery;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $cell;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $replyCode;

    private $status = true;

    /**
     * @var ArrayCollection
     */
    private $queries;

    /**
     * QuickReply constructor.
     *
     * @param string|null $value
     * @param null|string $valueWomen
     * @param null|string $valueMen
     * @param string|null $replyCode
     */
    public function __construct(
        ?string $value = null,
        ?string $valueWomen = null,
        ?string $valueMen = null,
        ?string $replyCode = null
    ) {
        if ($value) {
            $this->value = $value;
        } else {
            $this->value = $valueMen ?: $valueWomen;
        }

        $this->valueWomen       = $valueWomen;
        $this->valueMen         = $valueMen;
        $this->replyCode   = $replyCode;

        $this->queries = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getQuery(): ?Query
    {
        return $this->query;
    }

    public function setQuery(?Query $query): self
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * Get valueWomen
     *
     * @return null|string
     */
    public function getValueWomen(): ?string
    {
        return $this->valueWomen;
    }

    /**
     * Set valueWomen
     *
     * @param string $valueWomen
     * @return QuickReply
     */
    public function setValueWomen(string $valueWomen): self
    {
        $this->valueWomen = $valueWomen;

        return $this;
    }

    /**
     * Get valueMen
     *
     * @return null|string
     */
    public function getValueMen(): ?string
    {
        return $this->valueMen;
    }

    /**
     * Set valueMen
     *
     * @param string $valueMen
     * @return QuickReply
     */
    public function setValueMen(string $valueMen): self
    {
        $this->valueMen = $valueMen;

        return $this;
    }

    /**
     * @return Collection|Query[]
     */
    public function getQueries(): Collection
    {
        return $this->queries;
    }

    public function addQuery(Query $query): self
    {
        if (!$this->queries->contains($query)) {
            $this->queries[] = $query;
            $query->addDisplayCondition($this);
        }

        return $this;
    }

    public function removeQuery(Query $query): self
    {
        if ($this->queries->contains($query)) {
            $this->queries->removeElement($query);
            $query->removeDisplayCondition($this);
        }

        return $this;
    }

    public function getQueryReading(): ?QueryReading
    {
        return $this->queryReading;
    }

    public function setQueryReading(?QueryReading $queryReading): self
    {
        $this->queryReading = $queryReading;

        return $this;
    }

    /**
     * @return Collection|Query[]
     */
    public function getNextQuery()
    {
        return $this->nextQuery;
    }

    public function setNextQuery(?Query $nextQuery): self
    {
        $this->nextQuery = $nextQuery;

        return $this;
    }

    /**
     * @return string
     */
    public function getCell(): ?string
    {
        return $this->cell;
    }

    /**
     * @param string $cell
     */
    public function setCell(string $cell): void
    {
        $this->cell = $cell;
    }

    /**
     * @return string|null
     */
    public function getReplyCode(): ?string
    {
        return $this->replyCode;
    }

    /**
     * @param string|null $replyCode
     * @return QuickReply
     */
    public function setReplyCode(?string $replyCode): QuickReply
    {
        $this->replyCode = $replyCode;

        return $this;
    }

    /**
     * @return bool
     */
    public function isStatus(): bool
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus(bool $status): void
    {
        $this->status = $status;
    }
}
