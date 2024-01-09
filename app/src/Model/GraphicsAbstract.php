<?php

namespace App\Model;


abstract class GraphicsAbstract
{
    
     /**
     * Obtenir les données pour les envoyer vers le front
     */
    abstract public function getData($args, $specificGraphData);

     /**
     * Export des données + création des graphiques
     */
    abstract public function export($args);
}