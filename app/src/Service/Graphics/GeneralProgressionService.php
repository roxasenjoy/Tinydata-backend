<?php

namespace App\Service\Graphics;

use App\Entity\Content;
use App\Entity\User;
use App\Entity\UserClient;
use App\Entity\UserContents;
use App\Service\AcquisitionService;
use App\Service\DateService;
use App\Service\GlobalFilterService;
use Doctrine\Persistence\ManagerRegistry;

/*
    Graphique : GENERAL_PROGRESSION
    Permet d'obtenir la progression général d'un utilisateur sur une formation
*/
class GeneralProgressionService
{
    private $em;
    private $dateService;
    private $acquisitionService;
    private $filterService;

    public function __construct(ManagerRegistry $mr, DateService $dateService, AcquisitionService $acquisitionService, GlobalFilterService $filterService)
    {
        $this->em = $mr->getManager("tinycoaching");
        $this->dateService = $dateService;
        $this->acquisitionService = $acquisitionService;
        $this->filterService = $filterService;
    }


    public function getData($args)
    {
        // Solution temporaire - https://stackoverflow.com/questions/561066/fatal-error-allowed-memory-size-of-134217728-bytes-exhausted-codeigniter-xml#:~:text=You%20simply%20have%20the%20code,value%20to%20%22%2D1%22.&text=For%20anyone%20who%20needs%20set,%2Dd%20memory_limit%3D256M%20your_php_file.
        ini_set('memory_limit', '8G');
    
        $args['beginDate'] =  $this->dateService->getDate($args['beginDate'], $this->dateService::BEGIN);
        $args['endDate'] = $this->dateService->getDate($args['endDate'], $this->dateService::END);
        

        // CALCUL DE LA PROGRESSION GÉNÉRALE
        $genProgressionData = $this->acquisitionService->getUserGeneralProgression($args);


        // Récupération des informations des matrices
        $args['extended'] = false;

        $nbContentDone = $this->em->getRepository(UserContents::class)->getValidatedContentByGranularity($this->filterService::DEPTH_MATRIX, $args, null);   
        $nbContentByMatrix = $this->em->getRepository(Content::class)->getContentNumberByGranularity($this->filterService::DEPTH_MATRIX, $args);        
        $nbUsersByMatrix = $this->em->getRepository(User::class)->getNumberUserByMatrix($args);

        $ret = array();
        
        //Si vue non enrichie (donc vue classique)
        foreach($nbContentDone as $contentDone){
            foreach($nbContentByMatrix as $contentByMatrix){
                if($contentDone['id'] === $contentByMatrix['id']){
                    foreach($nbUsersByMatrix as $usersByMatrix){
                        if($contentDone['id'] === $usersByMatrix['id']){
                            array_push($ret, array(
                                "id"            => $contentDone['id'],
                                "name"          => $contentDone['name'],
                                "value"         => (int)ceil(($contentDone['total'] / ($contentByMatrix['total'] * $usersByMatrix['total']))*100),
                                'meanValue'     => $genProgressionData['userScore']
                            ));
                        }
                    }
                }
            }
        }

        return [
            'data' => $ret,
            'meanValue' => $genProgressionData['userScore']
        ];
    }

    
}
