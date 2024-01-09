<?php

namespace App\Service\Graphics;

use App\Entity\Matrix;
use App\Entity\Company;
use App\Entity\UserClient;
use App\Service\GlobalService;
use Doctrine\Persistence\ManagerRegistry;

/*
    Graphique : OPEN_ACCOUNTS
    Permet d'avoir la granularité de toutes les entreprises 
    avec le nombre de comptes ouverts par entreprise par formation
*/

class OpenAccountsService
{
    private $em;
    private $globalService;

    public function __construct(ManagerRegistry $mr, GlobalService $globalService)
    {
        $this->em = $mr->getManager("tinycoaching");
        $this->globalService = $globalService;
    }


    public function getData($args, $specificGraphData)
    {

        ini_set('memory_limit', '8G');

        $getAllOpenAccounts = $this->em->getRepository(Company::class)->getOpenAccounts($args);

        // dd($getAllOpenAccounts);
        $newArray = $this->em->getRepository(Matrix::class)->getMatrixSpecificData($args);
        $getEmails = $this->em->getRepository(UserClient::class)->getEmailOpenAccounts($args);

        foreach ($newArray as $key => $value) {
            $newArray[$key]['children'] = [];
        }

        foreach ($getAllOpenAccounts as $key => $value) {
            $value['value'] = intval($value['value']);
            $getAllOpenAccounts[$key] = $value;

            if ($value['formationId'] === null) {
                continue;
            }

            $key = array_search($value['formationId'], array_column($newArray, 'formationId'));
            if ($key === false) {
                //La formation n'existe pas dans $newArray, l'ajouter dans newArray ou continuer
                $newFormation = array(
                    "formationId"   => $value['formationId'],
                    "name"          => $value['formationName'],
                    "children"      => []
                );
                array_push($newArray, $newFormation);
                $key = array_search($value['formationId'], array_column($newArray, 'formationId'));
            }
            //On récupère la liste des organisations sous une formation
            $formationChildrens = $newArray[$key]['children'];

            //On met à jour la structure des enfants, ainsi que le nombre de compte ouvert à chaque niveau
            $values = $this->setupAccounts($formationChildrens, $value);

            $newArray[$key]['children'] = $values[0];
        }

        // Suppression des valeurs qui ne possède pas de formation -> Pour les données détaillées
        foreach ($getAllOpenAccounts as $keyAccount => $valueAccount) {

            // Si aucune liste d'email, on en créer une
            $getAllOpenAccounts[$keyAccount]['allEmails'] = [];

            // Suppression de l'élément dans notre liste
            if ($valueAccount['formationName'] === null || $valueAccount['value'] === 0) {
                unset($getAllOpenAccounts[$keyAccount]);
            } else {

                // Si c'est bon, on tape dans tous les emails disponibles.
                foreach ($getEmails as $valueEmail) {

                    // Si on tape sur le même élément, on rajoute l'utilisateur dans allEmails
                    if (
                        $valueAccount['formationId'] === $valueEmail['formationId']
                        && $valueAccount['companyId'] === $valueEmail['companyId']
                    ) {

                        if ($valueEmail['date'] === null) {
                            $valueEmail['date'] = '--/--/--';
                        } else {
                            $valueEmail['date'] = $valueEmail['date']->format('d/m/y G:i');
                        }

                        $valueEmail['roles'] = $valueEmail['roles'][0] === 'ROLE_USER' ? 'TINYCOACHING' : 'TINYDATA';

                        $getAllOpenAccounts[$keyAccount]['allEmails'][] = $dataEmailExtended[] = array(
                            'email'             => $valueEmail['email'],
                            'firstName'         => $valueEmail['firstName'],
                            'lastName'          => $valueEmail['lastName'],
                            'roles'             => $valueEmail['roles'],
                            'date'              => $valueEmail['date'],
                            'formation'         => $this->globalService->removeSpecialCharacters($valueEmail['formationName']),
                            'organisation'      => $valueEmail['parent1Name'] === null ? $valueEmail['companyName'] : $valueEmail['parent1Name'],
                            'sousOrganisation'  => $valueEmail['parent1Name'] !== null ? $valueEmail['companyName'] : '',
                        );

                        // $exportService->removeSpecialCharacters($valueEmail['formationName']);

                    }
                }
            }
        }

        // Ajout de l'entreprise mère 
        foreach ($getAllOpenAccounts as $key => $value) {
            for ($depth = 1; $depth <= 10; $depth++) {

                // Si c'est le parent (parent null + depth 1)
                if ($value['parent1Name'] === null && $depth === 1) {
                    $getAllOpenAccounts[$key]['subName'] = '';
                    break;
                }
                // S'il possède des enfants
                else if ($value['parent' . $depth . 'Id'] === null) {
                    $getAllOpenAccounts[$key]['subName']    = $value['name'];
                    $getAllOpenAccounts[$key]['name']       = $value['parent' . ($depth - 1) . 'Name'];
                    break;
                }
            }
        }

        //Ajout des comptes ouvert en fonction de la formation et de l'entreprise sélectionnée
        $array = array(
            'dataExtended' => $dataEmailExtended,
            'dataExtendedOld' => $getAllOpenAccounts,
            'dataGraphic'  => $newArray,
        );


        return array(
            'data' => json_encode($array)
        );
    }
    
    /**
     * $organisationsList : La liste des organisations de la formation (au niveau $depth)
     * $organisation : L'organisation à rajouter dans newArray
     * $depth : La profondeur actuelle
     */
    private function setupAccounts($organisationsList, $organisation, $depth = 1)
    {
        //Condition d'arrêt, si la profondeur $depth correspond à la profondeur de l'organisation
        if ($organisation['depth'] <= $depth) {
            //On retourne la liste des organisations mergé avec la nouvelle organisation, ainsi que le nombre de comptes à propager aux niveaux supérieurs
            return [array_merge($organisationsList, [$organisation]), $organisation['value']];
        }
        //Traitement si on doit "récurser"
        else {
            //On cherche l'ancêtre de $organisation à la profondeur $depth
            $key  = array_search($organisation['parent' . $depth . 'Id'], array_column($organisationsList, 'companyId'));

            //Si cet ancête n'existe pas (cas particulier), on l'initialise avec value 0 (personne n'a déjà fait cette formation)
            if ($key === false) {
                $newOrga = array(
                    'companyId' => $organisation['parent' . $depth . 'Id'],
                    'name'      => $organisation['parent' . $depth . 'Name'],
                    'value'     => 0
                );
                array_push($organisationsList, $newOrga);
                $key  = array_search($organisation['parent' . $depth . 'Id'], array_column($organisationsList, 'companyId'));
            }

            //Si l'ancêtre n'a pas d'attribut children (il n'a pas encore d'enfant), on initialise
            if (!array_key_exists('children', $organisationsList[$key])) {
                $organisationsList[$key]['children'] = [];
            }

            //On change la profondeur et on rappelle la même fonction
            $depth = $depth + 1;
            $values = $this->setupAccounts($organisationsList[$key]['children'], $organisation, $depth);

            //On remplace les enfants par les nouveaux enfants (avec l'organisation mergé)
            $organisationsList[$key]['children'] = $values[0];
            //On propage l'ajout du nombre de compte de $organisation
            $organisationsList[$key]['value'] += $values[1]; //$value[1] = $organisation['value']

            //On propage
            return [$organisationsList, $values[1]];
        }
    }
}
