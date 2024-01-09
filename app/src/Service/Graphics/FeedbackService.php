<?php

namespace App\Service\Graphics;

use App\Entity\Matrix;
use App\Entity\UserContents;
use App\Service\GranularityService;
use Doctrine\Persistence\ManagerRegistry;

/*
    Graphique : FEEDBACK
    Permet d'avoir tous les feedbacks (pas les contributions) des utilisateurs
*/

class FeedbackService
{

    const FACILE        = 'Facile';
    const DIFFICILE     = 'Difficile';
    const NEUTRE     = 'Neutre';
    const LONG          = 'Long';
    const BIEN          = 'Bien';

    private $granularityService;
    private $em;

    public function __construct(ManagerRegistry $mr, GranularityService $granularityService)
    {
        $this->em = $mr->getManager("tinycoaching");
        $this->granularityService = $granularityService;
    }


    public function getData($args, $specificGraphData)
    {
        $matrixAvailable = null;

        if ((!$specificGraphData['idGranularity'] || $specificGraphData['idGranularity'] === null) && $specificGraphData['formationZoom'] === "0") {

            // Sélection de la formation que l'utilisateur aura sélectionnée - Si aucune formation, on sélectionne une formation aléatoirement (dans les formations dispos)
            $matrixAvailable = $this->em->getRepository(Matrix::class)->findMatrixesForUser($args['organisationsFilter'], $args['matrixFilter'], $specificGraphData['setFormationSelected']);



            $matrixAvailable = $matrixAvailable === [] ? null : $matrixAvailable[0]->getId();

            $feedback = $this->em->getRepository(UserContents::class)->getFeedbacks($args, $specificGraphData);
        } else {
            // Afficher les granularités quand l'utilisateur souhaite avoir plus de détails sur celle qui vient de cliquer
            $feedback = $this->em->getRepository(UserContents::class)->getFeedbacks($args, $specificGraphData);
        }

        $res = [];
        $isData = false;

        // On tape sur tous les éléments pour le rajouter dans un autre tableau + on change les chiffres par des string compréhensibles.
        foreach ($feedback as $value) {

            if (in_array($value['feedback'], ['1', "2", "3", "4", "5"])) { // Il faut ajouter null pour afficher les 0 0 0 0

                $value['feedback'] === '1' ? $value['feedback'] = self::BIEN : $value['feedback'];
                $value['feedback'] === '2' ? $value['feedback'] = self::FACILE : $value['feedback'];
                $value['feedback'] === '3' ? $value['feedback'] = self::LONG : $value['feedback'];
                $value['feedback'] === '4' ? $value['feedback'] = self::DIFFICILE : $value['feedback'];
                $value['feedback'] === '5' ? $value['feedback'] = self::NEUTRE : $value['feedback'];
                $value['feedback'] === null ? $value['feedback'] = 'Aucun' : $value['feedback'];

                // On ajoute les valeurs qui nous intéressent
                $res['data'][$value['idGranularityFeedback']][$value['feedback']]                       = $value['total'];
                $res['data'][$value['idGranularityFeedback']]['name']                                   = $value['name'];
                $res['data'][$value['idGranularityFeedback']]['idGranularityFeedback']                  = $value['idGranularityFeedback'];

                $isData = true;
            }
        }

        if (!$isData) {
            $res['data'] = [];
        }

        foreach ($res['data'] as $value) {

            // On gère les cas où la granularité ne possède pas un feedback de chaque élément
            if (!array_key_exists(self::BIEN, $value)) {
                $value[self::BIEN] = 0;
            }

            if (!array_key_exists(self::FACILE, $value)) {
                $value[self::FACILE] = 0;
            }

            if (!array_key_exists(self::LONG, $value)) {
                $value[self::LONG] = 0;
            }

            if (!array_key_exists(self::DIFFICILE, $value)) {
                $value[self::DIFFICILE] = 0;
            }

            if (!array_key_exists(self::NEUTRE, $value)) {
                $value[self::NEUTRE] = 0;
            }

            // Calcul du nombre total de feedback pour une formation
            $total = $value[self::FACILE] + $value[self::LONG] + $value[self::DIFFICILE] + $value[self::BIEN] + $value[self::NEUTRE];

            // Changement de nom + rajout de la nouvelle valeur 
            array_push($res['data'][$value['idGranularityFeedback']], $total);
            $res['data'][$value['idGranularityFeedback']]['total'] = $res['data'][$value['idGranularityFeedback']][0];
            unset($res['data'][$value['idGranularityFeedback']][0]);

            if ($res['data'][$value['idGranularityFeedback']]['total'] === 0) {
                $res['data'][$value['idGranularityFeedback']]['total'] = 1;
            }

            // Calcul des nouvelles informations pour se baser sur 100%
            $res['data'][$value['idGranularityFeedback']][self::FACILE]        = round($value[self::FACILE] / $res['data'][$value['idGranularityFeedback']]['total'] * 100);

            $res['data'][$value['idGranularityFeedback']][self::DIFFICILE]     = round($value[self::DIFFICILE] / $res['data'][$value['idGranularityFeedback']]['total'] * 100);

            $res['data'][$value['idGranularityFeedback']][self::LONG]          = round($value[self::LONG] /  $res['data'][$value['idGranularityFeedback']]['total'] * 100);
            $res['data'][$value['idGranularityFeedback']][self::BIEN]          = round($value[self::BIEN] / $res['data'][$value['idGranularityFeedback']]['total'] * 100);
            $res['data'][$value['idGranularityFeedback']][self::NEUTRE]          = round($value[self::NEUTRE] / $res['data'][$value['idGranularityFeedback']]['total'] * 100);

            $res['data'][$value['idGranularityFeedback']]['name']              = $value['name'];
            $res['data'][$value['idGranularityFeedback']]['idGranularityFeedback']                = $value['idGranularityFeedback'];
        }


        /**
         * Gestion des donnée détaillées
         */

        $feedback_detailed = [];
        foreach ($res as $data) {
            foreach ($data as $element) {
                $nameFormation = $element['name'];
                unset($element['name']);
                unset($element['total']);
                unset($element['idGranularityFeedback']);

                $omitKey = $this->getAvailableFeedback($args);

                foreach ($element as $key => $value) {
                    if ($key !== $omitKey || $omitKey === null) {
                        $feedback_detailed[] = array(
                            'depth'         =>  $this->granularityService->getDepthName($specificGraphData['formationZoom']),
                            'name'          =>  $nameFormation,
                            'feedbackName'  =>  $key,
                            'percentage'    =>  $value
                        );
                    }
                }
            }
        }

        // On ajoute tous les commentaires des utilisateurs en fonction de la matrice sélectionnée
        $res['dataExtended'] = $feedback_detailed;

        if ($matrixAvailable) {
            $res['idMatrix'] = $matrixAvailable;
        } else {
            $res['idMatrix'] = $specificGraphData['setFormationSelected'];
        }

        return array(
            'data' => json_encode($res)
        );
    }

    /**
     * Compare les dates de l'utilisateur avec le 1er Juin 2023 pour connaître les feedback à afficher
     */
    private function getAvailableFeedback($args)
    {
        // Votre timestamp initial
        $beginDateTimestamp = strtotime($args['beginDate']);
        $endDateTimestamp = strtotime($args['endDate']);

        // Définissez votre timestamp de comparaison pour le 1er Juin 2023
        $compareDateTimestamp = strtotime('2023-06-01');

        if ($beginDateTimestamp > $compareDateTimestamp) {
            // Si la date de début est supérieure au 1er Juin 2023
            return "Neutre";
        } elseif ($beginDateTimestamp < $compareDateTimestamp && $endDateTimestamp > $compareDateTimestamp) {
            // Si la date de début est inférieure au 1er Juin 2023 et la date de fin supérieure au 1er Juin 2023
            return null;
        } elseif ($beginDateTimestamp < $compareDateTimestamp && $endDateTimestamp < $compareDateTimestamp) {
            // Si la date de début est inférieure au 1er Juin 2023 et la date de fin également inférieure au 1er Juin 2023
            return "Long";
        }
    }
}
