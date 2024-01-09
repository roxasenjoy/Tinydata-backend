<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QueryReadingRepository")
 */
class QueryReading
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Query", inversedBy="queryReadings")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $query;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\QuickReply", mappedBy="queryReading", orphanRemoval=true,
     *      cascade={"persist", "remove"})
     */
    private $quickReplies;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Reading", mappedBy="queryReading", cascade={"persist", "remove"})
     */
    private $readings;

    public function __construct()
    {
        $this->quickReplies = new ArrayCollection();
        $this->readings = new ArrayCollection();
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
     * @return Collection|QuickReply[]
     */
    public function getQuickReplies(): Collection
    {
        return $this->quickReplies;
    }

    public function addQuickReply(QuickReply $quickReply): self
    {
        if (!$this->quickReplies->contains($quickReply)) {
            $this->quickReplies[] = $quickReply;
            $quickReply->setQueryReading($this);
        }

        return $this;
    }

    public function removeQuickReply(QuickReply $quickReply): self
    {
        if ($this->quickReplies->contains($quickReply)) {
            $this->quickReplies->removeElement($quickReply);
            // set the owning side to null (unless already changed)
            if ($quickReply->getQueryReading() === $this) {
                $quickReply->setQueryReading(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Reading[]
     */
    public function getReadings(): Collection
    {
        return $this->readings;
    }

    public function addReading(Reading $reading): self
    {
        if (!$this->readings->contains($reading)) {
            $this->readings[] = $reading;
            $reading->setQueryReading($this);
        }

        return $this;
    }

    public function removeReading(Reading $reading): self
    {
        if ($this->readings->contains($reading)) {
            $this->readings->removeElement($reading);
            // set the owning side to null (unless already changed)
            if ($reading->getQueryReading() === $this) {
                $reading->setQueryReading(null);
            }
        }

        return $this;
    }
}
