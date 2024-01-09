<?php

namespace App\Entity\Traits;

use App\Entity\Company;
use Doctrine\ORM\Mapping as ORM;

trait ImportHistoryTrait
{

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $rank;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="importHistories")
     */
    private $company;



    public function getRank(): ?string
    {
        return $this->rank;
    }

    public function setRank(?string $rank): self
    {
        $this->rank = $rank;

        return $this;
    }


    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }
}
