<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity(repositoryClass="App\Repository\UserPlatformRepository")
 */
class UserPlatform{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     */
    private $id;


    /**
     * @ORM\Column(type="string",unique=true)
     */
    private $name;


    public function getId(){
        return $this->id;
    }


    public function getName(){
        return $this->name;
    }

    public function setName(String $name): self{
        $this->name = $name;
        return $this;
    }

}

?>