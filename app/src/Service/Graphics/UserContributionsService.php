<?php

namespace App\Service\Graphics;

use App\Entity\Matrix;
use App\Entity\UserContents;
use App\Service\GlobalService;
use App\Service\GranularityService;
use Doctrine\Persistence\ManagerRegistry;

/*
    Graphique : USER CONTRIBUTIONS
    Permet d'avoir toutes les contributions des utilisateurs
*/

class UserContributionsService
{

    private $granularityService;
    private $em;
    private $globalService;

    public function __construct(ManagerRegistry $mr, GranularityService $granularityService, GlobalService $globalService)
    {
        $this->em = $mr->getManager("tinycoaching");
        $this->granularityService = $granularityService;
        $this->globalService = $globalService;
    }


    public function getData($args, $specificGraphData)
    {
        $matrixAvailable = null;

        if ((!$specificGraphData['idGranularity'] || $specificGraphData['idGranularity'] === null) && $specificGraphData['formationZoom'] === "0") {

            // Sélection de la formation que l'utilisateur aura sélectionnée - Si aucune formation, on sélectionne une formation aléatoirement (dans les formations dispos)
            $matrixAvailable = $this->em->getRepository(Matrix::class)->findMatrixesForUser($args['organisationsFilter'], $args['matrixFilter'], $specificGraphData['setFormationSelected']);

            $matrixAvailable = $matrixAvailable === [] ? null : $matrixAvailable[0]->getId();

            // Gestion des commentaires
            $commentaires = $this->em->getRepository(UserContents::class)->getFeedbacksMessage($args, $specificGraphData);
            // dd($commentaires);

            $countCommentPerGranularity = $this->em->getRepository(UserContents::class)->getFeedbacksMessage($args, $specificGraphData, true);
        } else {
            $commentaires = $this->em->getRepository(UserContents::class)->getFeedbacksMessage($args, $specificGraphData);

            $countCommentPerGranularity = $this->em->getRepository(UserContents::class)->getFeedbacksMessage($args, $specificGraphData, true);

            if ($commentaires === []) {
                $args['formationZoom'] = 0;
                $commentaires = $this->em->getRepository(UserContents::class)->getFeedbacksMessage($args, $specificGraphData);
                $countCommentPerGranularity = $this->em->getRepository(UserContents::class)->getFeedbacksMessage($args, $specificGraphData, true);
            }
        }

        $feedbackDetailed = [];
        $total = 0;
        foreach ($commentaires as $value) {
            $feedbackDetailed[] = array(
                'firstName' => $value['firstName'],
                'lastName' => $value['lastName'],
                'email' => $value['email'],
                'groupePrincipal' => $value['parent1Name'] === null ? $value['companyName'] : $value['parent1Name'],
                'groupe' => $value['parent1Name'] === null ? '' : $value['companyName'],
                'message' => $this->globalService->removeSpecialCharacters($value['message']),
                'nameMatrix' => $value['nameMatrix'],
                'nameDomain' => $value['nameDomain'],
                'nameSkill' => $value['nameSkill'],
                'nameTheme' => $value['nameTheme'],
                'nameAcquis' => $value['nameAcquis'],
                'nameContent' => $value['nameContent']
            );
        }

        if ($matrixAvailable) {
            $idMatrix = $matrixAvailable;
        } else {
            $idMatrix = $specificGraphData['setFormationSelected'];
        }

        return array(
            'data' => json_encode(
                [
                    'idMatrix' => $idMatrix,
                    'dataExtended' => $feedbackDetailed,
                    'data' => $countCommentPerGranularity
                ]
            )
        );
    }
}
