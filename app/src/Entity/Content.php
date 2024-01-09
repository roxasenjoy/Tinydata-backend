<?php

namespace App\Entity;

use App\ORM\Tenant\TenantAwareInterface;
use App\ORM\Tenant\TenantTrait;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ContentRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 */
class Content implements TenantAwareInterface
{

    use TenantTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active = true;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Quiz", mappedBy="content", cascade={"persist","remove"}, fetch="EAGER")
     */
    private $quizzes;


    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Requirement", cascade={"persist"})
     */
    private $requirements;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Acquisition", inversedBy="tcContents")
     * @ORM\JoinColumn(onDelete="CASCADE")*
     */
    private $acquisition;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ValueAttribute",
     *     mappedBy="content", cascade={"persist", "remove"}, fetch="EAGER")
     */
    private $valueAttribute;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserContents", mappedBy="content", cascade={"persist", "remove"})
     */
    private $userContents;

    /**
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     */
    private $deletedAt;

    public function __construct()
    {
        $this->quizzes = new ArrayCollection();
        $this->requirements = new ArrayCollection();
        $this->valueAttribute = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection|Quiz[]
     */
    public function getQuizzes()
    {
        return $this->quizzes;
    }

    public function addQuiz(Quiz $quiz): self
    {
        if (!$this->quizzes->contains($quiz)) {
            $this->quizzes[] = $quiz;
            $quiz->setContent($this);
        }

        return $this;
    }

    public function removeQuiz(Quiz $quiz): self
    {
        if ($this->quizzes->contains($quiz)) {
            $this->quizzes->removeElement($quiz);
            // set the owning side to null (unless already changed)
            if ($quiz->getContent() === $this) {
                $quiz->setContent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Requirement[]
     */
    public function getRequirements(): Collection
    {
        return $this->requirements;
    }

    public function addRequirement(Requirement $requirement): self
    {
        if (!$this->requirements->contains($requirement)) {
            $this->requirements[] = $requirement;
        }

        return $this;
    }

    public function removeRequirement(Requirement $requirement): self
    {
        if ($this->requirements->contains($requirement)) {
            $this->requirements->removeElement($requirement);
        }

        return $this;
    }

    /**
     */
    public function getValueAttribute()
    {
        return $this->valueAttribute;
    }

    public function addValuesAttribute(ValueAttribute $valuesAttribute): self
    {
        if (!$this->valueAttribute->contains($valuesAttribute)) {
            $this->valueAttribute[] = $valuesAttribute;
            $valuesAttribute->setContent($this);
        }

        return $this;
    }

    public function removeValuesAttribute(ValueAttribute $valuesAttribute): self
    {
        if ($this->valueAttribute->contains($valuesAttribute)) {
            $this->valueAttribute->removeElement($valuesAttribute);
            // set the owning side to null (unless already changed)
            if ($valuesAttribute->getContent() === $this) {
                $valuesAttribute->setContent(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserContents()
    {
        return $this->userContents;
    }

    /**
     * @param mixed $userContents
     */
    public function setUserContents($userContents): void
    {
        $this->userContents = $userContents;
    }

    public function setAcquisition(?Acquisition $acquisition): self
    {
        $this->acquisition = $acquisition;

        return $this;
    }

    public function getAcquisition(): ?Acquisition
    {
        return $this->acquisition;
    }

    public function getSkill()
    {
        return $this->getAcquisition()->getSkillLevel()->getSkill();
    }

    public function getTheme()
    {
        return $this->getAcquisition()->getTheme();
    }

    public function getDomain()
    {
        return $this->getSkill()->getDomain();
    }

    public function getMatrix()
    {
        return $this->getDomain()->getMatrix();
    }

    public function getContentLevel() {
        return $this->getAcquisition()->getSkillLevel()->getLevel();
    }

    public $preview;
    public $access_token;

    /**
     * @ORM\Column(type="string", length=16, unique=true)
     */
    private $contentCode;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $memoQuestion;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $memoDefinition;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $updatedBy;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    public function getContentCode(): ?string
    {
        return $this->contentCode;
    }

    public function setContentCode(string $contentCode): self
    {
        $this->contentCode = $contentCode;

        return $this;
    }
    
    public function getFeedbackQuiz(): ?string
    {
        return $this->getQuizzes()[0]->getQuestions()[0]->getFeedbackQuiz();
    }
    
    public function setfeedbackQuiz(string $feedbackQuiz): self
    {
        $quizes = $this->getQuizzes();
        foreach ($quizes as $quiz) {
            $questions = $quiz->getQuestions();
            foreach($questions as $question){
                $question->setFeedbackQuiz($feedbackQuiz);
            }
        }

        return $this;
    }
    public function getMemoDefinition(): ?string
    {
        return $this->memoDefinition;
    }

    public function setMemoDefinition(?string $memoDefinition): self
    {
        $this->memoDefinition = $memoDefinition;

        return $this;
    }

    public function getMemoQuestion(): ?string
    {
        return $this->memoQuestion;
    }

    public function setMemoQuestion(?string $memoQuestion): self
    {
        $this->memoQuestion = $memoQuestion;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @param mixed $deletedAt
     * @return Content
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasReminder()
    {
        return $this->memoDefinition || $this->memoQuestion;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
