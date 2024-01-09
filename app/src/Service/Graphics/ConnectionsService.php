<?php

namespace App\Service\Graphics;

use App\Entity\UserClient;
use App\Entity\UserConnections;
use App\Entity\UserSession;
use App\Service\DateService;
use App\Service\StaticsData\TotalConnectionService;
use Doctrine\Persistence\ManagerRegistry;

/*
    Graphique : Compte le nombre de connexions des entreprises / utilisateur
*/

class ConnectionsService
{
    private $em;
    private $dateService;
    private $totalConnectionService;

    public function __construct(ManagerRegistry $mr, DateService $dateService, TotalConnectionService $totalConnectionService)
    {
        $this->em = $mr->getManager("tinycoaching");
        $this->dateService = $dateService;
        $this->totalConnectionService = $totalConnectionService;
    }

    public function getData($args, $specificGraphData)
    {

        $res = $this->em->getRepository(UserSession::class)->getConnections($args, $specificGraphData);
        $userNumber = $this->em->getRepository(UserClient::class)->getNumberOfUsersByCompanyId();

        $retData = array();

        $prevCompanyId = null;
        $currentCompanyObject = null;

        foreach ($res as $value) {

            if ($value['id'] !== $prevCompanyId) {
                if ($prevCompanyId !== null) {
                    array_push($retData, $currentCompanyObject);
                }
                $prevCompanyId = $value['id'];
                $currentCompanyObject = array(
                    "name"      => $value['name'],
                    "companyId" => $specificGraphData['groupByUser'] === 'true' ? null : $value['id'],
                    "data"      => []
                );
            }

            if ($specificGraphData['groupByUser'] === "true") {
                $totalConnection = $this->getTotalConnection($args, $specificGraphData, $value["id"]);
                $percentage = null;
                $text = null;
                if ($specificGraphData['precision'] === "DAY") {
                    $date = strtotime($value['year'] . "-" . $value['month'] . "-" . $value['day']);
                } else {
                    $date = strtotime($value['year'] . "-" . $value['month'] . "-01");
                }
            } else {
                $totalConnection = $value["cnt"];

                if ($specificGraphData['precision'] === "DAY") {
                    $percentage = round(($value['cnt'] / $userNumber[$value['id']]) * 100);
                    $date = strtotime($value['year'] . "-" . $value['month'] . "-" . $value['day']);
                    $text = ($value['day'] < 10 ? ("0" . $value['day']) : ($value['day'])) . "-" . ($value['month'] < 10 ? ("0" . $value['month']) : ($value['month'])) . "-" . $value['year'] . "\n" . $value['cnt'] . " co.";
                } else {
                    $percentage = round(($value['cnt'] / ($userNumber[$value['id']] * $this->days_in_month($value['month'], $value['year']))) * 100);
                    $date = strtotime($value['year'] . "-" . $value['month'] . "-01");
                    $text = ($value['month'] < 10 ? ("0" . $value['month']) : ($value['month'])) . "-" . $value['year'] . "\n" . $value['cnt'] . " co.";
                }
            }

            $currentCompanyObject["data"][] = array(
                "date"          => $date,
                "count"         => $value['cnt'],
                "totalCount"   => $totalConnection,
                "companyName"     => $value['name'],
                "percentage"    => (int)$percentage,
                "text"          => $text,
                "userFirstName" => $specificGraphData['groupByUser'] === 'true' ? $value['firstName'] : null,
                "userLastName"  => $specificGraphData['groupByUser'] === 'true' ? $value['lastName'] : null,
                "userEmail"     => $specificGraphData['groupByUser'] === 'true' ? $value['email'] : null,
            );

            // dump($currentCompanyObject);
        } 

 

        if ($currentCompanyObject !== null) {
            array_push($retData, $currentCompanyObject);
        }

        $dataExtended = [];
        foreach($retData as $value){
            // dump($value);
            foreach($value['data'] as $valueBis){
                // dd($valueBis);
                $dataExtended[] = array(
                    'date' => strftime("%d-%m-%Y", $valueBis['date']),
                    'count' => $valueBis['count'],
                    'totalCount' => $valueBis['totalCount'],
                    'companyName' => $valueBis['companyName'],
                    'userFirstName' => $valueBis['userFirstName'],
                    'userLastName' => $valueBis['userLastName'],
                    'userEmail' => $valueBis['userEmail']
                );
            }
        }

        return [
            'data' => json_encode(
                array(
                    "dataGraphic" => $retData,
                    "dataExtended" => $dataExtended
                )
            ) // Transformer le tableau en string pour le récupérer côté back
        ];
    }

    private function getTotalConnection($args, $specificGraphData, $value)
    {

        $args['beginDate'] =  $this->dateService->getDate($args['beginDate'], $this->dateService::BEGIN);
        $args['endDate'] = $this->dateService->getDate($args['endDate'], $this->dateService::END);

        $result =   $this->em->getRepository(UserSession::class)->getTotalConnectionForGraphicConnections($args, $value);

        return $result['0'][1];
    }

    private function days_in_month($month, $year)
    {
        // calculate number of days in a month
        return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
    }
}