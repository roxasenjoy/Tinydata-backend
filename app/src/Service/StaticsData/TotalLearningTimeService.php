<?php

namespace App\Service\StaticsData;

use App\Entity\User;
use App\Entity\UserSession;
use App\Entity\UserSessionDetails;
use App\Model\GraphicsAbstract;
use App\Service\DateService;
use Doctrine\Persistence\ManagerRegistry;

    /**
     * Données brutes Temps total passé dans l'apprentissage
     * 
     * Permet de connaitre le temps d'utilisation de Tinycoaching de l'apprennant ou de toutes les entreprises sélectionnées dans les filtres.
     * Le temps se base sur les intéractions entre toutes les intéractions de Tiny (Si 15 min sans intéraction, la session est fermée)
     * 
     * result = Le temps d'apprentissage total en SECONDE
     */
class TotalLearningTimeService extends GraphicsAbstract
{
    private $em;
    private $dateService;

    public function __construct(ManagerRegistry $mr, DateService $dateService)
    {
        $this->em = $mr->getManager("tinycoaching");
        $this->dateService = $dateService;
    }

    public function getData($args, $specificGraphData)
    {

        $args['beginDate'] =  $this->dateService->getDate($args['beginDate'], $this->dateService::BEGIN);
        $args['endDate'] = $this->dateService->getDate($args['endDate'], $this->dateService::END);  
        $extendedData = [];

        // dd($specificGraphData);

        if ($specificGraphData['isExtended'] == 'true') {
            $extendedData = $this->getExtendedData($args, $specificGraphData);
            // dd('test');
        } else {
            $extendedData['total'] = '';
            $extendedData['dataExtended'] = '';
        }

        $newResult = $this->em->getRepository(UserSession::class)->getTotalLearningTime($args);

        return [
            'data' => json_encode(
                array(
                    'value'         => (int)$newResult,
                    'dataExtended'  => $extendedData['dataExtended'],
                    'total'         => $extendedData['total']
                )
            ) // Transformer le tableau en string pour le récupérer côté back
        ];
    }

    public function export($args){

    }

    private function getExtendedData($args, $specificGraphData){

        $args['beginDate'] =  $this->dateService->getDate($args['beginDate'], $this->dateService::BEGIN);
        $args['endDate'] = $this->dateService->getDate($args['endDate'], $this->dateService::END);

        $result = $this->em->getRepository(UserSession::class)->getTotalLearningPerUser($args);
        $extendedData = $this->mergeSum($result);

        $total = count($result);
        // Eviter de limiter les données lorsqu'on filtre.
        if($specificGraphData['_pageIndex'] !== null){
            $nbBeginReturn = ($specificGraphData['_pageIndex'] - 1) * 10;
            $extendedData = array_slice($result, $nbBeginReturn, 10);
        }

        foreach($extendedData as $key => $value){
            $extendedData[$key] = array(
                'firstName' => $value['firstName'],
                'lastName'  => $value['lastName'],
                'email'     => $value['email'],
                'time'      => $this->dateService->secondsToTime((int)$value['total']),
                'lastInteraction' => $this->em->getRepository(UserSessionDetails::class)->getLastInteraction($result[$key]['ucId'])[0]['type'],
                'groupePrincipal' => $value['parent1Name'] === null ? $value['companyName'] : $value['parent1Name'],
                'groupe' => $value['parent1Name'] === null ? '' : $value['companyName'],           );
        }

        return array(
            'dataExtended' => $extendedData,
            'total' => $total
        );
    }

    function mergeSum($array) {
        $result = [];
    
        foreach ($array as $item) {
            $ucId = $item['ucId'];
            if (!isset($result[$ucId])) {
                $result[$ucId] = $item;
                $result[$ucId]['total'] = 0;
            }
    
            $result[$ucId]['total'] += $item['total'];
        }
    
        // retourne les valeurs du tableau sans les clés
        return array_values($result);
    }
    

    
}