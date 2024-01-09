<?php


namespace App\Resolver;

use App\Entity\AnalyticsLeitner;
use App\Entity\UserContents;
use App\Entity\User;
use App\Service\DateService;

class FilterResolver extends AbstractResolver
{

        /** 
    * @var DateService
    **/
    private $dateService;

    public function __construct(DateService $dateService)
    {
        $this->dateService = $dateService;
    }

    /**
     * Trier les données en fonction des filtres de l'utilisateur
     * @param $beginDate String
     * @param $endDate String
     * @param $parcours
     * @param User $user
     * @throws \Exception
     */
    public function filter(
        $beginDate,
        $endDate,
        $parcours,
        $entreprise)
    {

        $filterValue = []; // Liste qui contiendra tous les éléments

        $beginDate = $this->dateService->getDate($beginDate, $this->dateService::BEGIN);
        $endDate = $this->dateService->getDate($endDate, $this->dateService::END);

        $dataFilter = $this
            ->em
            ->getRepository(UserContents::class)
            ->findContent(
                $beginDate,
                $endDate,
                $parcours,
                $entreprise);

        while (current($dataFilter)) {
            $filterValue[] = array(
                "id" => current($dataFilter)['id'],
                "date" => current($dataFilter)['date'],
                "parcours" => current($dataFilter)['onBoardingProfile'],
                "entreprise" => current($dataFilter)['name']);
            next($dataFilter);
        }
        return $filterValue;
    }


    /**
     * Filtre se trouvant en dessous des graphiques
     * Objectif : Séparer les formations qui possèdent des données avec les formations qui n'en possèdent pas.
     */
    public function getFormationsWithData($companies, $matrixFilter, $userId, $nameFilter, $beginDate, $endDate){

        $hasData = [];
        $result = [];
        $matrixes = $this->em->getRepository(User::class)->getAllMatrixes($companies, $matrixFilter, $userId);
        $getAllMatrixes = false;

        // dump($matrixes);
        
        switch($nameFilter){
            case 'Performance':
                $hasData = $this->em->getRepository(UserContents::class)->getMatrixWithPerformance($companies, $matrixFilter, $userId, $beginDate, $endDate);
                $getAllMatrixes = true;
                break;
            case 'Feedback':
                $hasData = $this->em->getRepository(UserContents::class)->getMatrixWithFeedback($companies, $matrixFilter, $userId, $beginDate, $endDate);
                $getAllMatrixes = true;
                break;
            case 'UserContributions':
                $hasData = $this->em->getRepository(UserContents::class)->getMatrixWithFeedbackMessage($companies, $matrixFilter, $userId, $beginDate, $endDate);
                $getAllMatrixes = true;
                break;
            case 'GeneralProgression':
                // Afficher les progressions générales si elles sont supérieurs à 0*
                break;
            case 'ContentValid':
                break;
            case 'RappelsMemo':
                $hasData = $this->em->getRepository(AnalyticsLeitner::class)->getMatrixWithRappelsMemo($companies, $matrixFilter, $userId, $beginDate, $endDate);
                $getAllMatrixes = true;
                break;
            default:   
        }

        if($getAllMatrixes){
            $hasData = count($hasData) === 0 ? [] : $hasData;

            // Tableau des différences
    
            foreach ($matrixes as $matrice) {
                $found = false;
                $matrice['matrixID'] = $matrice['id'];
                foreach ($hasData as $matriceWithData) {

                    
                    // Données présentes
                    if ($matrice["matrixID"] === $matriceWithData["matrixID"]) {
                        $matrice['hasData'] = true;
                        $result[] = $matrice;
                        $found = true;
                        break;
                    }
                }

                // Aucune donnée
                if (!$found) {
                    $matrice['hasData'] = false;
                    $result[] = $matrice;
                }
            }       

            usort($result, function($a, $b) {
                if ($a['hasData'] === $b['hasData']) {
                    return strcmp($a['name'], $b['name']);
                }
                return $a['hasData'] ? -1 : 1;
            });
        } else {
            foreach($matrixes as $matrice){
                $matrice['matrixID'] = $matrice['id'];
                $matrice['hasData'] = true;
                $result[] = $matrice;
            }
        }
        

        return $result;
    }
}
