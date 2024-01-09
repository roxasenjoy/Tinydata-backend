<?php

namespace App\Service\Graphics;

use App\Entity\AnalyticsLeitner;
use Doctrine\Persistence\ManagerRegistry;

/*
    Graphique : RAPPELS MEMORIELS
*/

class RappelsMemorielsService
{
    private $em;

    public function __construct(ManagerRegistry $mr)
    {
        $this->em = $mr->getManager("tinycoaching");
    }


    public function getData($args, $specificGraphData)
    {

        /**
         * Type de données à obtenir : 
         *      - Ordonnée -> Pourcentage de mémorisation (3/8, 2/8, 7/8) + pourcentage de rappels échoués / nombre de rappels effectués 
         *      - Abscisse -> Nom de l'acquis 
         * 
         * Données à return : 
         *      - barData(mémorisation) -> [50,45,97]
         *      - lineData(échecs) -> [20,30,40]
         *      - Abscisse(nom acquis) -> ['Nom 1', 'Nom 2', 'Nom 3']
         */
        $getLeitner = $this->em->getRepository(AnalyticsLeitner::class)->getAnalyticsLeitner($args, $specificGraphData);

        $result = [
            'barData' => [],
            'lineData' => [],
            'acquisData' => [],
            'dataExtended' => []
        ];

        $summarizedData = [];

        // Initialisation des compteurs et des sommes de pourcentages
        $count = 1;
        $sum_percentage_failed = 0;
        $sum_percentage_validation = 0;

        foreach ($getLeitner as $item) {
            // Création d'une identification unique pour chaque élément
            $identification = $item['matrixId'] . '-' . $item['domainId'] . '-' . $item['skillId'] . '-' . $item['themeId'] . '-' . $item['acquisId'];

            // Calcul du pourcentage d'échecs en fonction du nombre de mémoires faites et ratées
            $percentage_failed = $item['nb_memory_failed'] === 0 ? 0 : ($item['nb_memory_failed'] / $item['nb_memory_done'] * 100);
            $percentage_failed = $this->roundedNumber($percentage_failed);

            // Calcul du pourcentage de validation en fonction de la boîte courante
            $percentage_validation =  $item['current_box'] / 8 * 100;

            // Ajout des données détaillées à $result['extended']
            $result['dataExtended'][] = $this->addExtendedData($item);

            // Si l'identification n'est pas encore dans $summarizedData, initialiser et ajouter les données
            if (!isset($summarizedData[$identification])) {

                $count = 1;
                $sum_percentage_failed = 0;
                $sum_percentage_validation = 0;

                // Ajout des données résumées
                $summarizedData[$identification] = array(
                    "alId" =>  $item['alId'],
                    "current_box" => $percentage_validation,
                    "percentage_failed" => $percentage_failed,
                    "nb_memory_done" => $item['nb_memory_done'],
                    "nb_memory_failed" => $item['nb_memory_failed'],
                    "acquisName" => $item['acquisName']
                );

                // Mise à jour des sommes de pourcentages
                $sum_percentage_failed += $percentage_failed;
                $sum_percentage_validation += $percentage_validation;
            } else {
                // Si l'identification est déjà dans $summarizedData, mettre à jour les données
                $sum_percentage_failed += $percentage_failed;
                $sum_percentage_validation += $percentage_validation;
                $count += 1;

                // Mise à jour des pourcentages et des compteurs
                $summarizedData[$identification]['percentage_failed'] = $this->roundedNumber($sum_percentage_failed / $count);
                $summarizedData[$identification]['nb_memory_done'] += $item['nb_memory_done'];
                $summarizedData[$identification]['nb_memory_failed'] += $item['nb_memory_failed'];
                $summarizedData[$identification]['current_box'] = $this->roundedNumber($sum_percentage_validation / $count);
            }
        }

        // Ajout des données résumées aux données de barre, de ligne et d'acquisition du tableau $result
        foreach ($summarizedData as $value) {
            array_push($result['barData'], $value['current_box']);
            array_push($result['lineData'], $value['percentage_failed']);
            array_push($result['acquisData'], $value['acquisName']);
        }

        // Retourne le tableau $result en tant que string JSON
        return array(
            'data' => json_encode($result)
        );
    }


    /**
     * Ajout des données détaillées
     */
    private function addExtendedData($item)
    {

        $percentage_failed = $item['nb_memory_failed'] === 0 ? 0 : ($item['nb_memory_failed'] / $item['nb_memory_done'] * 100);
        $percentage_failed = $this->roundedNumber($percentage_failed);

        return array(
            'firstName' => $item['firstName'],
            'lastName'  => $item['lastName'],
            'email'     => $item['userEmail'],
            'groupePrincipal' => $item['parent1Name'] === null ? $item['companyName'] : $item['parent1Name'],
            'groupe' => $item['parent1Name'] === null ? '' : $item['companyName'],
            'matrixName' => $item['matrixName'],
            'acquisName' => $item['acquisName'],
            'current_box' => $item['current_box'] . ' / 8',
            "memory_retention_rate" => $this->roundedNumber($item['current_box'] / 8 * 100) . '%',
            "percentage_failed" => $percentage_failed . '%',
        );
    }


    private function roundedNumber($number)
    {
        return number_format($number, 1, '.', '');
    }
}
