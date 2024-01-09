<?php

namespace App\Service;


/**
 * Class DateService
 * This service is here to format
 * @package App\Service
 */
class DateService
{

    const BEGIN = "beginDate";
    const END = "endDate";
    const TINY_START_DATE = "2014-01-01 00:00:00";

    public function __construct(){}

    public function secondsToTime($seconds) {

        $resultInSeconds = intval($seconds);
    
        $years = floor($resultInSeconds / (3600 * 24 * 30 * 12));
        $months = floor(($resultInSeconds % (3600 * 24 * 30 * 12)) / (3600 * 24 * 30));
        $days = floor((($resultInSeconds % (3600 * 24 * 30 * 12)) % (3600 * 24 * 30)) / (3600 * 24));
        $hours = floor(((($resultInSeconds % (3600 * 24 * 30 * 12)) % (3600 * 24 * 30)) % (3600 * 24)) / 3600);
        $minutes = floor((((($resultInSeconds % (3600 * 24 * 30 * 12)) % (3600 * 24 * 30)) % (3600 * 24)) % 3600) / 60);
        $seconds = floor(((((($resultInSeconds % (3600 * 24 * 30 * 12)) % (3600 * 24 * 30)) % (3600 * 24)) % 3600) % 60));
    
        $yearsDisplay = $years > 0 ? $years . "a " : "";
        $monthsDisplay = $months > 0 ? $months . "m " : "";
        $daysDisplay = $days > 0 ? $days . "j " : "";
        $hoursDisplay = $hours > 0 ? $hours . "h " : "";
        $minutesDisplay = $minutes > 0 ? $minutes . "min " : "";
        $secondsDisplay = $seconds > 0 ? $seconds . "s" : "";
    
        if ($resultInSeconds === 0) {
            return '--';
        }
    
        if ($years > 0) {
            return trim($yearsDisplay) . " " . trim($monthsDisplay) . " " . trim($daysDisplay) . " " . trim($hoursDisplay);
        }
    
        if ($months > 0) {
            return trim($monthsDisplay) . " " . trim($daysDisplay) . " " . trim($hoursDisplay) . " " . trim($minutesDisplay);
        }
    
        if ($days > 0) {
            return trim($daysDisplay) . " " . trim($hoursDisplay) . " " . trim($minutesDisplay);
        }
    
        if ($hours > 0) {
            return trim($hoursDisplay) . " " . trim($minutesDisplay);
        }
    
        if ($minutes > 0) {
            return trim($minutesDisplay) . " " . $secondsDisplay;
        }
    
        return $secondsDisplay;
    }
    

    public function getDate($date, $type)
    {
        if($date){
            $date = new \DateTime($date);
        } else {
            if (empty($date) && $type === self::BEGIN){
                //$date = new \DateTime("2014-01-01 00:00:00");
                $date = new \DateTime('now');
                $date->modify("-6 months");
            }

            if (empty($date) && $type === self::END){
                $date = new \DateTime('now');
            }        
        }
        $date = $date->format('Y-m-d H:i:s');
        return $date;
    }

    public static function dateToFrench($date, $format) 
    {
        $english_days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $french_days = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
        $english_months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
        $french_months = array('Janvier', 'Fevrier', 'BLBL', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Sept.', 'Oct.', 'Nov.', 'Dec.');
        $date =  date($format, strtotime($date));
        $date = str_replace($english_months, $french_months, $date);
        $date = str_replace($english_days, $french_days, $date);
        $date = str_replace("BLBL", "Mars", $date);
        return $date;
    }
}
