<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuestionRepository")
 */
class Question
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Quiz", inversedBy="questions")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $quiz;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Response", mappedBy="question", cascade={"persist", "remove"})
     */
    private $responses;

    /**
     * @ORM\Column(type="text")
     */
    private $value;
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $multiResponse = 0;
    
    /**
     * @ORM\Column(type="text")
     */
    private $feedbackQuiz;


    public function __construct()
    {
        $this->responses = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): self
    {
        $this->quiz = $quiz;

        return $this;
    }

    /**
     * @return Collection|Response[]
     */
    public function getResponses(): Collection
    {
        return $this->responses;
    }


    public function setResponses($responses): self
    {
        $this->responses = $responses;
        return $this;
    }

    public function addResponse(Response $response): self
    {
        if (!$this->responses->contains($response)) {
            $this->responses[] = $response;
            $response->setQuestion($this);
        }

        return $this;
    }

    public function removeResponse(Response $response): self
    {
        if ($this->responses->contains($response)) {
            $this->responses->removeElement($response);
            // set the owning side to null (unless already changed)
            if ($response->getQuestion() === $this) {
                $response->setQuestion(null);
            }
        }

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getMultiResponse(): ?bool
    {
        return count($this->getGoodAnswers()) > 1;
    }

    public function setMultiResponse(bool $multiResponse): self
    {
        $this->multiResponse = $multiResponse;

        return $this;
    }

    public function getFeedbackQuiz(): ?string
    {
        return $this->feedbackQuiz;
    }

    public function setFeedbackQuiz(string $feedbackQuiz): self
    {
        $this->feedbackQuiz = $feedbackQuiz;

        return $this;
    }

    public function getGoodAnswers()
    {
        $gResponses=array();

        foreach ($this->getResponses() as $response) {
            if ($response->getIsCorrect()) {
                $gResponses[]=$response->getId();
            }
        }
        return $gResponses;
    }
}
