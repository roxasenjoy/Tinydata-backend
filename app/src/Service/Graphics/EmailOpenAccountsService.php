<?php

namespace App\Service\Graphics;

use App\Entity\UserClient;
use Doctrine\Persistence\ManagerRegistry;

/*
    Graphique : OPEN_ACCOUNTS
    Permet d'obtenir tous les emails des apprenants
*/
class EmailOpenAccountsService
{
    private $em;

    public function __construct(ManagerRegistry $mr)
    {
        $this->em = $mr->getManager("tinycoaching");
    }

    public function getData($args)
    {
        return $this->em->getRepository(UserClient::class)->getEmailOpenAccounts($args);
    }

    
}
