<?php

namespace App\Entity\Traits;

use App\Entity\Acquisition;
use App\Entity\UserContents;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityNotFoundException;
use Gedmo\Mapping\Annotation as Gedmo;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait UserClientTrait
 * @package App\Entity\Traits
 */
trait UserClientTrait
{
    /**
     * @ORM\Column(name="inscription_date", type="datetime", nullable=true)
     */
    private $inscriptionDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $cgu = false;

    private $token;

    public function getInscriptionDate(): ?DateTimeInterface
    {
        return $this->inscriptionDate;
    }

    public function setInscriptionDate(DateTimeInterface $inscriptionDate): self
    {
        $this->inscriptionDate = $inscriptionDate;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }


    public function getCgu(): ?bool
    {
        return $this->cgu;
    }

    public function setCgu(bool $cgu): self
    {
        $this->cgu = $cgu;

        return $this;
    }


    public function getDay()
    {
        if ($this->getInscriptionDate()) {
            $interval = $this->getInscriptionDate()->diff(new DateTime());

            return $interval->format('%a') ;
        }

        return null;
    }

    /**
     * FO V1
     * get list of contents consumed by user :
     *  - validated
     *  - not validated and not later
     * @return array
     *
     */
    public function getContents()
    {
        $contents = array();
        /**
         * @var UserContents $usercontent
         */
        foreach ($this->getUserContents() as $usercontent) {
            /***Content validated or saved for later**/
            if ($usercontent->getStatus() || $usercontent->getLater()) {
                $content = $usercontent->getContent();
                $contents[] = $content;
            }
        }
        return $contents;
    }

    public function getValidContents()
    {
        $_validContents = array();
        foreach ($this->getUserContents() as $userContent) {
            if ($userContent->getStatus()== true) {
                $_validContents[] = $userContent->getContent();
            }
        }

        return $_validContents;
    }

    public function getFailedContents()
    {
        $_validContents = array();
        /** @var UserContents $userContent */
        foreach ($this->getUserContents() as $userContent) {
            if (!$userContent->getStatus() && !$userContent->getLater()) {
                $_validContents[] = $userContent->getContent();
            }
        }

        return $_validContents;
    }

    public function getAttribute($attr)
    {
        /**
         * @var BoardingValue $boardingValue *
         */
        foreach ($this->boardingValues as $boardingValue) {
            if ($boardingValue->getBoardingAttributes()->getName() == $attr) {
                return in_array($attr, array("firstname", "lastname")) ?
                    ucfirst(strtolower($boardingValue->getValue())) : $boardingValue->getValue();
            }
        }

        return null;
    }
}
