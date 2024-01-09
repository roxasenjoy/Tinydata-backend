<?php

namespace App\Service\StaticsData;

use App\Entity\Content;
use App\Entity\Matrix;
use App\Entity\UserContents;
use App\Model\GraphicsAbstract;
use App\Service\DateService;
use App\Service\GlobalFilterService;
use App\Service\GlobalService;
use Doctrine\Persistence\ManagerRegistry;

/*
*   Données brutes : Nombre total de contenus validés/échoués
*/

class TotalContentService extends GraphicsAbstract
{
    private $em;
    private $dateService;
    private $filterService;
    private $globalService;

    public function __construct(ManagerRegistry $mr, DateService $dateService, GlobalService $globalService, GlobalFilterService $filterService)
    {
        $this->em = $mr->getManager("tinycoaching");
        $this->dateService = $dateService;
        $this->filterService = $filterService;
        $this->globalService = $globalService;
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
            $extendedData['total'] = '';
            $extendedData['dataExtended'] = '';
        }

        $result = $this->em->getRepository(Content::class)->getTotalContent($args, $specificGraphData);

        return [
            'data' => json_encode(
                array(
                    "value" => $result,
                    "total" => $this->computeTotalContent($args, $specificGraphData),
                    "totalStatus" => $extendedData['total'],
                    "dataExtended" => $extendedData['dataExtended'],
                )
            ) // Transformer le tableau en string pour le récupérer côté back
        ];
    }

    public function export($args)
    {
    }

    public function getExtendedData($args, $specificGraphData)
    {

        // Liste des contenus validés en fonction du userId
        $allContentsValidated = $this->em->getRepository(Content::class)->getContentStatusForAllUsers($args, $specificGraphData);
        $specificGraphData['_pageIndex'] = null;
        $total = count($this->em->getRepository(Content::class)->getContentStatusForAllUsers($args, $specificGraphData));

        foreach ($allContentsValidated as $key => $value) {
            $allContentsValidated[$key]['matrixName'] = $this->globalService->removeSpecialCharacters($allContentsValidated[$key]['matrixName']);
            $allContentsValidated[$key]['companyName'] = $value['parent1Name'] === null ? $value['companyName'] : $value['parent1Name'];
            $allContentsValidated[$key]['parent1Name'] = $value['parent1Name'] !== null ? $value['companyName'] : '';
        }

        return  [
            'dataExtended' => $allContentsValidated,
            'total'         => $total
        ];
    }



    /********************************************************************************
     * 
     *                              Fonctions secondaires
     * 
     ******************************************************************************/

    //Retourne le nombre de contenus à réaliser au total pour la sélection d'utilisateur
    private function computeTotalContent($args, $specificGraphData)
    {
        $numberOfUserByMatrix = $this->em->getRepository(Matrix::class)->getNumberOfUserByMatrix($args);
        $matrixIds = [];

        foreach ($numberOfUserByMatrix as $matrix) {
            array_push($matrixIds, $matrix['id']);
        }

        $specificGraphData['nameDepth'] = $this->filterService::DEPTH_MATRIX;
        $numberOfContentByMatrix = $this->em->getRepository(Content::class)->getContentNumberByGranularity($args, $specificGraphData);

        $totalContent = 0;
        foreach ($numberOfContentByMatrix as $numberContent) {
            foreach ($numberOfUserByMatrix as $numberUser) {
                if ($numberContent['id'] === $numberUser['id']) {
                    $totalContent += ($numberUser['count'] * $numberContent['total']);
                    break;
                }
            }
        }
        return $totalContent;
    }
}
