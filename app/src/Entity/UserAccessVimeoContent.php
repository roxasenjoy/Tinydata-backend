<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Specific Entity to store access vimeo HelloMyBot
 * @ORM\Entity()
 */
class UserAccessVimeoContent
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column()
     * @var string
     */
    private $link;

    /**
     * @ORM\Column()
     * @var string
     */
    private $accessToken;


    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $link
     * @return UserAccessVimeoContent
     */
    public function setLink(string $link): UserAccessVimeoContent
    {
        $this->link = $link;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     * @return UserAccessVimeoContent
     */
    public function setAccessToken(string $accessToken): UserAccessVimeoContent
    {
        $this->accessToken = $accessToken;

        return $this;
    }
}