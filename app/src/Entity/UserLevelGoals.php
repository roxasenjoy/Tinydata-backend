<?php
/**
 * Created by PhpStorm.
 * User: habibamous
 * Date: 26/11/18
 * Time: 10:05 ص
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class UserLevelGoals
 * @ORM\Table(name="user_level_goals")
 * @ORM\Entity(repositoryClass="App\Repository\UserLevelGoalsRepository")
 */
class UserLevelGoals
{
    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="App\Entity\Profile", inversedBy="userLevelGoals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $profile;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="App\Entity\Level", inversedBy="userLevelGoals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $level;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="App\Entity\Skill", inversedBy="userLevelGoals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $skill;

    /**
     * UserLevelGoals constructor.
     * @param $profile
     * @param $level
     * @param $skill
     */
    public function __construct($profile = null, $level = null, $skill = null)
    {
        $this->profile = $profile;
        $this->level = $level;
        $this->skill = $skill;
    }


    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    public function getLevel(): ?Level
    {
        return $this->level;
    }

    public function setLevel(?Level $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getSkill(): ?Skill
    {
        return $this->skill;
    }

    public function setSkill(?Skill $skill): self
    {
        $this->skill = $skill;

        return $this;
    }
}
