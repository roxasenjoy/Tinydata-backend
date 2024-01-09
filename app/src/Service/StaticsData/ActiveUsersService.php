<?php

namespace App\Service\StaticsData;

use App\Entity\UserClient;
use Doctrine\Persistence\ManagerRegistry;

/*
    Données brutes : Nombre de comptes actifs
*/

class ActiveUsersService
{
    private $em;

    public function __construct(ManagerRegistry $mr)
    {
        $this->em = $mr->getManager("tinycoaching");
    }

    public function getData($args, $specificGraphData)
    {


        $activeUsers = $this->em->getRepository(UserClient::class)->getUsersOnBorder($args);
        $allUsers = $this->em->getRepository(UserClient::class)->getUsersCompanies($args, "false");
        $allExtendedData = $this->em->getRepository(UserClient::class)->getUsersCompanies($args, $specificGraphData);

        $startDateFilter = new \DateTime($args['beginDate']);
        $endDateFilter = new \DateTime($args['endDate']);

        $extendedData = array();

        if($specificGraphData['isExtended'] === 'true'){
            $count_actif_user = 0;
            foreach ($allExtendedData as &$item) {
    
                if ($this->isUserDisabled($item, $startDateFilter, $endDateFilter)) {
                    $item['lastConnection'] = "Désactivé";
    
                } elseif (is_null($item['firstConnection'])) {
                    $item['lastConnection'] = "Par défaut";
    
                } else {
                    $item['lastConnection'] = "Actif";
                    $count_actif_user++;
                }
    
                array_push($extendedData, array(
                    'firstName' => $item['firstName'],
                    'lastName' => $item['lastName'],
                    'email' => $item['email'],
                    'lastConnection' => $item['lastConnection'],
                    'groupePrincipal' => $item['parent1Name'] === null ? $item['companyName'] : $item['parent1Name'],
                    'groupe' => $item['parent1Name'] === null ? '' : $item['companyName'],
                ));
            }
        }
       

        return [
            'data' => json_encode(
                array(
                    "value" => $activeUsers,
                    "total" => $allUsers,
                    "dataExtended" => $extendedData
                )
            ) // Transformer le tableau en string pour le récupérer côté back
        ];
    }

    function isUserDisabled($item, $startDateFilter, $endDateFilter)
    {
        return $item['enabled'] === false ||
            $this->isContractOutsideFilter($item, $startDateFilter, $endDateFilter) ||
            $this->isInscriptionOutsideFilter($item, $startDateFilter, $endDateFilter);
    }

    function isContractOutsideFilter($item, $startDateFilter, $endDateFilter)
    {
        return $item['contractDateBegin'] > $endDateFilter ||
            $item['contractDateEnd'] < $startDateFilter;
    }

    function isInscriptionOutsideFilter($item, $startDateFilter, $endDateFilter)
    {
        return $item['inscriptionDate'] < $startDateFilter ||
            $item['inscriptionDate'] > $endDateFilter;
    }
}
