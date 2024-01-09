<?php

namespace App\Entity;

use App\Constants\CompanyContracts;
use Doctrine\ORM\Mapping as ORM;
use PhpParser\Node\Stmt\Switch_;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ContractTypeRepository")
 */
class CompanyContractType {


    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;


    public function getId(){
        return $this->id;
    }


    public function getName(): string{
        return $this->name;
    }

    public function setName($name): self {
        $this->name = $name;
        return $this;
    }


    public function __toString(): string {
        switch ($this->name) {
            case CompanyContracts::TYPE_USER_LICENSE:
                return "Par utilisateur / mois";
            default:
                return "Par groupe / an";
        }
    }
}

?>