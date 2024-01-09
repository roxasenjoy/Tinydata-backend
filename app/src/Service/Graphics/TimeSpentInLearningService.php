<?php

namespace App\Service\Graphics;

use App\Entity\User;
use App\Entity\UserSession;
use App\Entity\UserSessionDetails;
use App\Model\GraphicsAbstract;
use App\Service\DateService;
use Doctrine\Persistence\ManagerRegistry;

/*
    Graphique : Temps passé dans l'apprentissage
*/

class TimeSpentInLearningService extends GraphicsAbstract
{


    // Jour
    const dateDay       = 'day';
    const getDay        = 'l';  // 24 (Noel Youpi !)

    // Semaines
    const getWeekDay    = 'l';      // Samedi (Week end)

    // Mois
    const dateMonth     = 'month';
    const getMonth      = 'M';      // Août (Vacances !!)

    // Années
    const dateYear      = 'year';
    const getYear       = 'Y';       // 2021 (Covid bruh)

    private $em;
    private $dateService;

    public function __construct(ManagerRegistry $mr, DateService $dateService)
    {
        $this->em = $mr->getManager("tinycoaching");
        $this->dateService = $dateService;
    }

    public function getData($args, $specificGraphData)
    {

        if ($specificGraphData['isExtended'] === "true") {
            // Récupérer les données des données détaillées  
            return $this->getExtendedData($args);
        } else {
            // Récupérer les données pour le graphique
            return $this->getGraphicData($args);
        }
    }

    private function getExtendedData($args)
    {
        $args['beginDate'] =  $this->dateService->getDate($args['beginDate'], $this->dateService::BEGIN);
        $args['endDate'] = $this->dateService->getDate($args['endDate'], $this->dateService::END);

        $result = $this->em->getRepository(UserSession::class)->getTotalLearningPerUser($args);
        $result = $this->mergeSum($result);

        foreach ($result as $key => $value) {

            $result['dataExtended'][$key] = array(
                'firstName' => $value['firstName'],
                'lastName'  => $value['lastName'],
                'email'     => $value['email'],
                'total'     => (int)$value['total'],
                'time'      => $this->dateService->secondsToTime((int)$value['total']),
                'lastInteraction' => $this->em->getRepository(UserSessionDetails::class)->getLastInteraction($result[$key]['ucId'])[0]['type']
            );
        }

        return [
            'data' => json_encode($result)
        ];
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
    

    private function getGraphicData($args)
    {
        $args['beginDate'] =  $this->dateService->getDate($args['beginDate'], $this->dateService::BEGIN);
        $args['endDate'] = $this->dateService->getDate($args['endDate'], $this->dateService::END);

        // Obtenir l'intervale entre les deux dates du filtre DATE
        $datetime1 = date_create($args['beginDate']);
        $datetime2 = date_create($args['endDate']);
        $interval = (array) date_diff($datetime1, $datetime2); // d = days - m = month - y = year

        $result = $this->em->getRepository(UserSession::class)->getTotalLearningTimePerDate($args);

        /**
         * Obtenir toutes les valeurs qui nous intéressent afin de pouvoir les traiter du côté front
         *  - $graphValue (Temps total passé dans l'apprentissage)
         */

        // Jours
        if ($interval['y'] === 0 && $interval['m'] === 0 && $interval['d'] < 1) {
            $graphValue = $this->getTotalTimePerDay($result);
        }

        // Semaine
        else if ($interval['y'] === 0 && $interval['m'] === 0 && $interval['d'] < 7) {
            $graphValue = $this->getTotalTimePerDay($result);
        }

        //Mois
        else if ($interval['y'] === 0 && $interval['m'] < 12) {
            $graphValue = $this->getTotalTimePerMonth($result);
        }

        //Années
        else {
            $graphValue = $this->getTotalTimePerYear($result);
        }

        /**
         * Maintenant qu'on a les données concernant le graph, il nous faut aussi la moyenne du temps total passé dans l'apprentissage pour la globalité
         */
        $meanLearning = 0; // On verra ça plus tard ! Moyenne du temps d'apprentissage

        // On harmonise tout ça, on créant notre array final qui correspondra à notre return (Pour le front) 
        $finalResult['name'] = $graphValue['name'];
        $finalResult['data'] = $graphValue['time'];
        $finalResult['meanLearning'] = $meanLearning;

        return [
            'data' => json_encode($finalResult) // Transformer le tableau en string pour le récupérer côté back
        ];
    }

    private function getTotalTimePerDay($result)
    {
        $dayOrder = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $totalTimePerDay = [];
        foreach ($result as $record) {
            $dayInEnglish = $record['date']->format('l');  // Get the day of the week
            $day = $this->translateDayToFrench($dayInEnglish);  // Translate the day to French
            if (!array_key_exists($day, $totalTimePerDay)) {
                $totalTimePerDay[$day] = 0;
            }
            $totalTimePerDay[$day] += $record['totalTime'];
        }
        uksort($totalTimePerDay, function ($a, $b) use ($dayOrder) {
            return array_search($a, $dayOrder) - array_search($b, $dayOrder);
        });
        return [
            'name' => array_keys($totalTimePerDay),
            'time' => array_values($totalTimePerDay)
        ];
    }

    private function getTotalTimePerMonth($result)
    {
        $monthOrder = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        $totalTimePerMonth = [];
        foreach ($result as $record) {
            $monthInEnglish = $record['date']->format('F');  // Get the month
            $month = $this->translateMonthToFrench($monthInEnglish);  // Translate the month to French
            if (!array_key_exists($month, $totalTimePerMonth)) {
                $totalTimePerMonth[$month] = 0;
            }
            $totalTimePerMonth[$month] += $record['totalTime'];
        }
        uksort($totalTimePerMonth, function ($a, $b) use ($monthOrder) {
            return array_search($a, $monthOrder) - array_search($b, $monthOrder);
        });
        return [
            'name' => array_keys($totalTimePerMonth),
            'time' => array_values($totalTimePerMonth)
        ];
    }

    private function getTotalTimePerYear($result)
    {
        $totalTimePerYear = [];
        foreach ($result as $record) {
            $year = $record['date']->format('Y');  // Get the year
            if (!array_key_exists($year, $totalTimePerYear)) {
                $totalTimePerYear[$year] = 0;
            }
            $totalTimePerYear[$year] += $record['totalTime'];
        }
        ksort($totalTimePerYear); // Sort years in ascending order
        return [
            'name' => array_keys($totalTimePerYear),
            'time' => array_values($totalTimePerYear)
        ];
    }

    private function translateDayToFrench($dayInEnglish)
    {
        $daysEnglish = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $daysFrench = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];

        return str_replace($daysEnglish, $daysFrench, $dayInEnglish);
    }

    private function translateMonthToFrench($monthInEnglish)
    {
        $monthsEnglish = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $monthsFrench = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

        return str_replace($monthsEnglish, $monthsFrench, $monthInEnglish);
    }

    public function export($args)
    {
    }
}