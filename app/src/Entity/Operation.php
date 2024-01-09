<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\JoinColumn;
use DateTime;
use DateTimeZone;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OperationRepository")
 */
class Operation
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="App\Entity\OperationType",inversedBy="operations")
     * @JoinColumn(nullable=false)
     */
    private $operationType;


    /**
     * @ManyToOne(targetEntity="App\Entity\User", inversedBy="operations")
     * @JoinColumn(nullable=false)
     */
    private $adminUser;

    /**
     * @ManyToOne(targetEntity="App\Entity\Company",inversedBy="operations")
     * @JoinColumn(nullable=false,referencedColumnName="id")
     */
    private $company;

    /**
     * @ManyToOne(targetEntity="App\Entity\Company",inversedBy="operations")
     * @JoinColumn(nullable=true)
     */
    private $companyTransferTo;

    /**
     * @ManyToMany(targetEntity="App\Entity\User", inversedBy="operations")
     */
    private $users;

    /**
     * @ORM\Column(type="datetime")
     */    
    private $createdAt;
    /**
     * @ORM\Column (type="integer", nullable=true)
     */
    private $nbUsers;


    public function __construct()
    {
        $this->createdAt = new DateTime('now', new DateTimeZone('Europe/Paris'));
    }

    public function getId() {
        return $this->id;
    }

    public function getOperationType(){
        return $this->operationType;
    }

    public function getCompany(){
        return $this->company;
    }

    public function getCompanyTransferTo(){
        return $this->companyTransferTo;
    }

    public function getUsers(){
        return $this->users;
    }

    public function getCreatedAt(){
        return $this->createdAt;
    }

    public function getAdminUser(){
        return $this->adminUser;
    }

    public function setOperationType($type){
        $this->operationType = $type;
        return $this;
    }
    
    public function setCompany($companyId){
        $this->company = $companyId;
        return $this;
    }

    public function setCompanyTransferTo($companyId){
        $this->companyTransferTo = $companyId;
        return $this;
    }

    public function setUsers($users){
        $this->users = $users;
        $this->nbUsers = sizeof($users);
        return $this;
    }

    public function setAdminUser($userId){
        $this->adminUser = $userId;
        return $this;
    }

    /**
     * if same date
     *  returns "Il y a X heures/minutes"
     * else
     *  returns "Le dd/mm/YY à H:m"
     * 
     * @return String
     */
    public function getDateText(): String{
        $currentDate = new DateTime();
        $d = $this->getCreatedAt();
        $diff = date_diff($currentDate,$d);
        if ($diff->d >=1) return "Le ".$d->format('d/m/Y à H:i:s');

        if ($diff->h <= 1) return "Il y a ".($diff->i)." minutes";

        return $diff->format("Il y a %h heures");
    }
    /**
     * if same date
     *  returns "Aujourd'hui à H:m"
     * else
     *  returns "Le dd/mm/YY à H:m"
     * 
     * @return String
     */
    public function getDateTextV2(): String{
        $currentDate = new DateTime();
        $d = $this->getCreatedAt();
        if ($currentDate->format('d/m/Y') == $d->format('d/m/Y'))
            return "Aujourd'hui à ".$d->format("H:i");
        
        return "Le ".$d->format('d/m/Y à H:i');
    }

    /**
     * @return mixed
     */
    public function getNbUsers()
    {
        if ($this->nbUsers)
            return $this->nbUsers;
        if ($this->users)
            return sizeof($this->users);


        return 0;
    }

    /**
     * @param mixed $nbUsers
     */
    public function setNbUsers($nbUsers): self
    {
        $this->nbUsers = $nbUsers;
        return $this;
    }
}

?>