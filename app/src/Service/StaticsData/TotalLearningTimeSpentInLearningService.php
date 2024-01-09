<?php

namespace App\Service\StaticsData;

use App\Entity\UserSession;
use App\Service\DateService;
use Doctrine\Persistence\ManagerRegistry;

/*
    DFJKSDJFLSDKLF
*/
class TotalLearningTimeSpentInLearningService
{

    
    // Jour
    CONST dateDay       = 'day';
    CONST getDay        = 'l';  // 24 (Noel Youpi !)

    // Semaines
    CONST getWeekDay    = 'l';      // Samedi (Week end)

    // Mois
    CONST dateMonth     = 'month'; 
    CONST getMonth      = 'M';      // Août (Vacances !!)

    // Années
    CONST dateYear      = 'year';
    CONST getYear       = 'Y';       // 2021 (Covid bruh)

    private $em;
    private $dateService;

    public function __construct(ManagerRegistry $mr, DateService $dateService)
    {
        $this->em = $mr->getManager("tinycoaching");
        $this->dateService = $dateService;
    }


    public function getData($args)
    {

        $args['beginDate'] =  $this->dateService->getDate($args['beginDate'], $this->dateService::BEGIN);
        $args['endDate'] = $this->dateService->getDate($args['endDate'], $this->dateService::END);

        // Obtenir l'intervale entre les deux dates du filtre DATE
        $datetime1 = date_create( $args['beginDate']);
        $datetime2 = date_create( $args['endDate']);
        $interval = (array) date_diff($datetime1, $datetime2); // d = days - m = month - y = year

        $result = $this->em->getRepository(UserSession::class)->getTotalLearningTimePerDate($args);   

        /**
         * Obtenir toutes les valeurs qui nous intéressent afin de pouvoir les traiter du côté front
         *  - $graphName (Nom des jours, mois, années)
         *  - $graphValue (Temps total passé dans l'apprentissage)
         */

        // Jours
        if($interval['y'] === 0 && $interval['m'] === 0 && $interval['d'] < 1){
            $graphName  = $this->getFrenchDateToArray($interval['d'], $args['beginDate'], self::dateDay, self::getDay);
            $graphValue = $this->getValueToArray($result, self::getDay);
        } 

        // Semaine
        else if($interval['y'] === 0 && $interval['m'] === 0 && $interval['d'] < 7){
            $graphName = $this->getFrenchDateToArray($interval['d'], $args['beginDate'], self::dateDay, self::getWeekDay); // Return X jours après la date de début
            $graphValue = $this->getValueToArray($result, self::getWeekDay);
        }

        //Mois
        else if($interval['y'] === 0 && $interval['m'] < 12){ 
            $graphName = $this->getFrenchDateToArray($interval['m'], $args['beginDate'], self::dateMonth, self::getMonth); // Return X jours après la date de début
            $graphValue = $this->getValueToArray($result, self::getMonth);
        }

        //Années
        else{
            $graphName = $this->getFrenchDateToArray($interval['y'], $args['beginDate'], self::dateYear, self::getYear); // Return X jours après la date de début 
            $graphValue = $this->getValueToArray($result, self::getYear);      
        }


        // Si la valeur n'est pas présent dans $graphValue mais qu'elle l'est dans $graphName, on ajoute un 0 que ça soit pareil !
        $fixGraphValue = [];
        foreach($graphName as $keyName => $valueName){

            // Si l'ordonnée n'est pas présente dans $graphValue, on le met à 0
            if(!array_key_exists($valueName, $graphValue)){
                array_push($fixGraphValue, 0);
            } else {
                $seconds = $graphValue[$valueName]; // Le temps récupéré est en seconde
                array_push($fixGraphValue, $seconds); // Normalement il faut mettre : $graphValue[$valueName]
            }
            
        }

        /**
         * Maintenant qu'on a les données concernant le graph, il nous faut aussi la moyenne du temps total passé dans l'apprentissage pour la globalité
         */
        $meanLearning = 0; // On verra ça plus tard ! Moyenne du temps d'apprentissage

        // On harmonise tout ça, on créant notre array final qui correspondra à notre return (Pour le front) 
        $finalResult['name'] = $graphName;
        $finalResult['data'] = $fixGraphValue;
        $finalResult['meanLearning'] = $meanLearning;
        
        return $finalResult;
    }

    

    private function getValueToArray($result, $format){
        $graphValue = [];

        foreach ($result as $key => $value){

            // On change le format pour qu'il soit pareil que getFrenchDateToArray utilisé dans getTotalTimeSpentInLearning
            $dateToString = $keyDate = $value['date']->format($format);
    
            // Vu qu'on a pas besoin de changer le nom en francais, on l'exclu l'année de la traduction francaise
            if($format !== self::getYear){
                $keyDate = $this->dateService->dateToFrench($dateToString , $format);
               // $keyDate === 'Mars' ? $keyDate = 'Mar' : $keyDate; // Incompréhensible : Un S s'ajoute au jour Mardi - Marsdi
            }
            
            if (array_key_exists($keyDate, $graphValue)){
                $graphValue[$keyDate] += $value['totalTime'];
            } else{
                $graphValue[$keyDate] = $value['totalTime'];
            }
        } 

        return $graphValue;
    }

    /**
     * Change les dates ENG to FR 
     * Lien du tutoriel : https://lucidar.me/fr/web-dev/in-php-how-to-display-date-in-french/
     * $daysDiff: Int
     * return : Array
     */
    private function getFrenchDateToArray($daysDiff, $beginDate, $dateToAdd, $format){

        $countDays = 0;
        $dateArray = [];

        while ($countDays <= $daysDiff) {

            // On ajoute +1 Days pour avoir toutes les journées de la date de début à la date de fin
            $frenchDate = $this->dateService->dateToFrench($beginDate , $format); // Transforme le nom des dates en Francais

           // $frenchDate === 'Mars' ? $frenchDate = 'Mar' : $frenchDate;
            array_push($dateArray, $frenchDate);
            $beginDate = date('Y-m-d H:i:s', strtotime($beginDate . ' +1 '. $dateToAdd));
            $countDays += 1;
        }
        
        return $dateArray;
    }

    
}