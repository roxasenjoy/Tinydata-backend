<?php

namespace App\Service;

use Doctrine\Persistence\ManagerRegistry;

/**
 * Class GlobalFilterService
 *
 * @package App\Service
 */
class GlobalService
{
    private $em;

    public function __construct(ManagerRegistry $mr)
    {
        $this->em = $mr->getManager("tinycoaching");
    }

     /**
     * Supprime les caractères spéciaux pour l'export car les caractères spéciaux cassent l'EXCEL et le CSV MARCHE MER 65
     * Il est utilité pour 
     */
    public function removeSpecialCharacters($string) {
        // Supprimer les caractères spéciaux en utilisant une expression régulière
        $string = preg_replace('/[^A-Za-z0-9 ]/', '', $string);
        
        return $string;
    }

}
