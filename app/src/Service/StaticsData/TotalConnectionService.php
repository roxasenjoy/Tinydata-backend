<?php

namespace App\Service\StaticsData;

use App\Entity\UserConnections;
use App\Entity\UserSession;
use App\Model\GraphicsAbstract;
use App\Service\DateService;
use Doctrine\Persistence\ManagerRegistry;

/*
    Données brutes : Nombre de connexions totux
*/

class TotalConnectionService extends GraphicsAbstract
{

    private $dateService;
    private $em;

    public function __construct(ManagerRegistry $mr, DateService $dateService)
    {
        $this->em = $mr->getManager("tinycoaching");
        $this->dateService = $dateService;
    }

    public function getData($args, $specificGraphData)
    {

        $extendedData = [];
        $args['beginDate'] =  $this->dateService->getDate($args['beginDate'], $this->dateService::BEGIN);
        $args['endDate'] = $this->dateService->getDate($args['endDate'], $this->dateService::END);

        // Récupération des données détaillées 
        if ($specificGraphData['isExtended'] === 'true') {
            $extendedData = $this->getExtendedData($args, $specificGraphData);
        } else {
            $extendedData['total'] = 0;
            $extendedData['result'] = '';
        }

        $newResult = $this->em->getRepository(UserSession::class)->getNumberOfConnectionsPerUser($args);

        $totalConnexion = 0;
        foreach ($newResult as $result) {
            $totalConnexion += $result['totalConnexion'];
        }

        return [
            'data' => json_encode(
                array(
                    'value' => $totalConnexion,
                    'total' => $extendedData['total'],
                    'dataExtended' => $extendedData['result']
                )
            ) // Transformer le tableau en string pour le récupérer côté back
        ];
    }

    public function export($filters)
    {
        return [];
    }

    private function getExtendedData($args, $specificGraphData)
    {
        $specificGraphData['groupByUser'] = 'true';
        $specificGraphData['precision'] = '';
        $userConnection = $this->em->getRepository(UserSession::class)->getUserModalData($args, $specificGraphData);
        
        foreach ($userConnection['query'] as $key => $value) {
            // Créez la clé "date" avec le format "month/year"
            $userConnection['query'][$key]['date'] = (intval($value['day']) < 10 ? '0' .$value['day'] : $value['day']) . '/' . (intval($value['month']) < 10 ? '0' .$value['month'] : $value['month']) . '/' . $value['year'];
            $userConnection['query'][$key]['organisation'] = $value['parent1Name'] === null ? $value['companyName'] : $value['parent1Name'];
            $userConnection['query'][$key]['sousOrganisation'] = $value['parent1Name'] !== null ? $value['companyName'] : '';
        }

        return array(
            'result' => $userConnection['query'],
            'total'=> $userConnection['countElements']
        );

    }
}