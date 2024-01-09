<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\Column;
/**
 * @Entity(repositoryClass="App\Repository\OperationNotificationRepository")
 */
class OperationNotification{


    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="App\Entity\User", inversedBy="operationNotification")
     * @JoinColumn()
     */
    private $user;
    
    /**
     * @ManyToOne(targetEntity="App\Entity\Operation",inversedBy="operationNotification")
     * @JoinColumn()
     */
    private $operation;
    
    /**
     * @Column(type="boolean", options={"default": false})
     */
    private $isRead;


    public function __construct(){
        $this->isRead = false;
    }

    public function getId(){
        return $this->id;
    }

    public function getUser(){
        return $this->user;
    }

    public function setUser($user){
        $this->user = $user;
        return $this;
    }

    public function getOperation(){
        return $this->operation;
    }
    public function setOperation($operation){
        $this->operation = $operation;
        return $this;
    }

    public function getIsRead(){
        return $this->isRead;
    }
    public function setIsRead($isRead){
        $this->isRead = $isRead;
        return $this;
    }

}
?>