<?php

namespace App\Service\Graphics;

use App\Entity\Content;
use App\Entity\Matrix;
use App\Entity\UserContents;
use Doctrine\Persistence\ManagerRegistry;

/*
    Graphique : CONTENTS_VALID
    Permet de connaître tous les contenus validés de la formation sélectionée
*/
class ContentsValidService
{
    private $em;

    public function __construct(ManagerRegistry $mr)
    {
        $this->em = $mr->getManager("tinycoaching");
    }

    public function getData($args, $specificGraphData)
    {

        // La formation est NULL ? Il faut sélectionner une formation aléatoirement parmis celles disponibles (En fonction du user bien sûr)
        if(!$specificGraphData['setFormationSelected'] || $specificGraphData['setFormationSelected'] === null){
            $matrixAvailable = $this->em->getRepository(Matrix::class)->findMatrixesForUser($args['organisationsFilter'], $args['matrixFilter']);
            $specificGraphData['setFormationSelected'] = $matrixAvailable[0]->getId();
            $formationNameSelected = $matrixAvailable[0]->getName();
        } else {
            $formationNameSelected = $this->em->getRepository(Matrix::class)->find($specificGraphData['setFormationSelected'])->getName();
        }

        // Liste de tous les contenus
        $allContents = $this->em->getRepository(Content::class)->getAllContents($args, $specificGraphData);

        // Liste des contenus validés en fonction du userId
        $allContentsValidated = $this->em->getRepository(UserContents::class)->getAllContentsValidated($args, $specificGraphData);

        $arrayMerge = array_unique(array_merge($allContentsValidated,$allContents), SORT_REGULAR);

        // Etat des contenus
        $validated          = 0;
        $failed             = 0;
        $neverDone          = 0;
        $total              = 0;

        foreach($arrayMerge as $i){
            $id = $i['contentId'];
            foreach($i as $key => $input_identifier){
                // Compte l'état des contenus
                if($input_identifier === true){
                    $validated +=1;
                } elseif ($input_identifier === false){
                    $failed +=1;
                }

                $output[$id][$key] = $input_identifier; 
            }
        }

        $output = array_values($output);
        $total = sizeof($output);
        $arrayToDelete = array("," , "-");

        foreach($output as $value){

            // Le cours n'est pas effectué
            if (!array_key_exists('status', $value)) {

                $neverDone += 1;

                $data[] = array(
                    'nameDomain'        => str_replace($arrayToDelete, "", $value['nameDomain']),
                    'nameSkill'         => str_replace($arrayToDelete, "", $value['nameSkill']),
                    'nameTheme'         => str_replace($arrayToDelete, "", $value['nameTheme']),
                    'nameAcquis'        => str_replace($arrayToDelete, "", $value['nameAcquis']),
                    'nameContent'       => str_replace($arrayToDelete, "", $value['nameCNT']),
                    'category'          => 2,
                    'categoryName'      => $this->getCategoryName(2),
                    'id'                => $value['contentId'],
                    'nameMatrix'        => str_replace($arrayToDelete, "", $formationNameSelected),
                );

            // Le cours est effectué - Validé ou échoué
            } else {
                $data[] = array(
                    'nameDomain'        => str_replace($arrayToDelete, "", $value['nameDomain']),
                    'nameSkill'         => str_replace($arrayToDelete, "", $value['nameSkill']),
                    'nameTheme'         => str_replace($arrayToDelete, "", $value['nameTheme']),
                    'nameAcquis'        => str_replace($arrayToDelete, "", $value['nameAcquis']),
                    'nameContent'       => str_replace($arrayToDelete, "", $value['nameCNT']),
                    'category'          => +$value['status'],
                    'categoryName'      => $this->getCategoryName(+$value['status']),
                    'id'                => $value['contentId'],
                    'nameMatrix'        => str_replace($arrayToDelete, "", $formationNameSelected),
                );
            }
        }

        //Maintenant on doit gérer les liens entre les nodes
        $linkNodes = array();
        $countData = count($data);
        $arrayOfElements = [];
        $arrayNumberElements = [];

        // On tape X fois en fonction de la taille des données
        for($y=0; $y < $countData; $y++){

            $sourceRandom = $y;
            $targetRandom = $y + 1;

            array_push($arrayOfElements, $sourceRandom);
            array_push($arrayNumberElements, $y);

            $linkNodes[] = array(
                'source' => $sourceRandom,
                'target' => $targetRandom
            );
        }

        //On récupere toutes les différences entre le tableau de toutes les nodes et le tableau créé aléatoirement
        $diff = array_diff($arrayNumberElements, $arrayOfElements);
        foreach($diff as $key => $value){
            $linkNodes[] = array(
                'source' => $value,
                'target' => $value + 1
            );
        }
    
        $result = array(
            'nameMatrix'        => $formationNameSelected,
            'idMatrix'          => $specificGraphData['setFormationSelected'],
            'contentValidated'  => $validated,
            'contentFailed'     => $failed,
            'contentNeverDone'  => $neverDone,
            'contentTotal'      => $total,
            'data'              => $data,
            'link'              => $linkNodes
        );

        return array(
            'data' => json_encode(
                array(
                    'dataGraphics' => $result,
                    'dataExtended' => $result['data']
                )
            )
        );

    }

    function addNodes($linkNodes, $number){

        // Pour toutes les nodes qui ne possèdent pas de lien, on rajoute un lien vers l'élément d'après : 500 -> 501 / 400 -> 401 / 250 -> 251
        foreach($linkNodes as $key => $value){

            $linkNodes[] = array(
                'source' => $key,
                'target' => $key * $number
            );
        }

        return $linkNodes;
    }

    function unique_multi_array($array, $key) { 
        $temp_array = array(); 
        $i = 0; 
        $key_array = array(); 
        
        foreach($array as $val) { 
            if (!in_array($val['source'], $key_array) && !in_array($val['target'], $key_array)) { 
                $key_array[$i] = $val[$key]; 
                $temp_array[$i] = $val; 
            } 
            $i++; 
        } 
        return $temp_array; 
    }

    // Détermine le nom de la categorie
    public function getCategoryName($categoryId){
        switch($categoryId){
            case 0;
                return 'Non validé ❌';
            case 1:
                return 'Validé ✅';
            case 2:
                return 'Non effectué';
        }
    }

    
}