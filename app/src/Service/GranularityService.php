<?php

namespace App\Service;

use Doctrine\Persistence\ManagerRegistry;

/**
 * Class GranularityService
 *
 * @package App\Service
 */
class GranularityService
{ 

    public function __construct(ManagerRegistry $mr)
    {
        $this->em = $mr->getManager("tinycoaching");
    
    }

    public function getDepthName($depth = 0){
        $array = ['Formation', 'Domaine', 'Compétence', 'Thème', 'Acquis', 'Contenu'];
        return $array[$depth];
    }



}
