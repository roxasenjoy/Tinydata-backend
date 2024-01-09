<?php

namespace App\Entity;

use App\Constants\TcTextParams;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TcTextConditionRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class TcTextCondition
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    private $variable;

    private $hour;
    private $date;
    private $weather;
    private $quiz;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Company")
     */
    private $groups;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $conditionCategory;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $condition_label;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TcText", inversedBy="conditions")
     */
    private $tcText;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $cronFrequency;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $pushedAt;

    /**
     * @ORM\Column (type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     * @return TcTextCondition
     */
    public function setId($id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVariable()
    {
        return $this->variable;
    }

    /**
     * @param mixed $variable
     */
    public function setVariable($variable): self
    {
        $this->variable = $variable;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConditionCategory()
    {
        return $this->conditionCategory;
    }

    /**
     * @param $conditionCategory
     * @return TcTextCondition
     */
    public function setConditionCategory($conditionCategory): self
    {
        $this->conditionCategory = $conditionCategory;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConditionLabel()
    {
        return $this->condition_label;
    }

    /**
     * @param $condition_label
     * @return TcTextCondition
     */
    public function setConditionLabel($condition_label): self
    {
        $this->condition_label = $condition_label;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTcText()
    {
        return $this->tcText;
    }

    /**
     * @param $tcText
     * @return TcTextCondition
     */
    public function setTcText($tcText): self
    {
        $this->tcText = $tcText;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHour()
    {
        if ($this->condition_label == "hour" || $this->condition_label == "frequency") {
            $this->hour = DateTime::createFromFormat('H', $this->getVariable());
        }
        return $this->hour;
    }

    /**
     * @param mixed $hour
     */
    public function setHour($hour): void
    {
        if ($this->condition_label == "hour" || $this->condition_label == "frequency") {
            $this->setVariable($hour->format('H'));
        }
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        if (($this->condition_label == "holiday" || $this->condition_label == "update_date") && $this->getVariable()) {
            $date = DateTime::createFromFormat('d-m-Y H:i', $this->getVariable());
            $this->date = $date->format('Y-m-d H:i');
        }
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date): void
    {
        if (($this->condition_label == "holiday" || $this->condition_label == "update_date") && $date) {
            $date = DateTime::createFromFormat('Y-m-d H:i', $date);
                $this->setVariable($date->format("d-m-Y H:i"));
        }
    }

    /**
     * @return mixed
     */
    public function getWeather()
    {
        if ($this->condition_label == "weather") {
            $this->weather = $this->getVariable();
        }
        return $this->weather;
    }

    /**
     * @param mixed $weather
     */
    public function setWeather($weather): void
    {
        if ($this->condition_label == "weather") {
            $this->setVariable($weather);
        }
    }

    /**
     * @return mixed
     */
    public function getQuiz()
    {
        if ($this->condition_label == "quiz") {
            $this->quiz = $this->getVariable();
        }
        return $this->quiz;
    }

    /**
     * @param mixed $quiz
     */
    public function setQuiz($quiz): void
    {
        if ($this->condition_label == "quiz") {
            $this->setVariable($quiz);
        }
    }

    public function getCronFrequency()
    {
        return $this->cronFrequency;
    }

    public function setCronFrequency($cronFrequency) : self
    {
        $this->cronFrequency = $cronFrequency;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getPushedAt()
    {
        return $this->pushedAt;
    }

    public function setPushedAt($pushedAt): self
    {
        $this->pushedAt = $pushedAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist()
     */
    public function setPushedAtValue()
    {
        $this->pushedAt = new DateTime('now');
    }

    /**
     * @ORM\PrePersist()
     */
    public function setCreatedAt()
    {
        $this->createdAt = new DateTime('now');
    }

    /**
     * @return mixed
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param mixed $groups
     * @return TcTextCondition
     */
    public function setGroups($groups): TcTextCondition
    {
        $this->groups = $groups;
        return $this;
    }

}
