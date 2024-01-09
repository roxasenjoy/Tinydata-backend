<?php


namespace App\Resolver;

use App\Entity\Acquisition;
use App\Entity\Company;
use App\Entity\Domain;
use App\Entity\Matrix;
use App\Entity\Skill;
use App\Entity\Theme;
use App\Entity\User;

class ResearchBarResolver extends AbstractResolver
{

    /**
     * Barre de recherche pour le zoom utilisateur
     * 
     * Obtiens tous les utilisateurs contenant $value dans le nom || prenom || email
     */
    public function researchUsers($value){
        $accounts = $this->em->getRepository(User::class)->researchUsers($value);
        return $accounts;
    }



    /**
     * Génère toutes les données de la barre de recherche.
     */
    public function researchBar($researchBar)
    {
        /* TODO : Rendre le code propre */

        $returnValues = []; // Liste qui contiendra tous les éléments

        // Récupération de tous les éléments de la DB
        $companies = $this->em->getRepository(Company::class)->findCompaniesTinydata($researchBar); // Filtre activé 
        $users = $this->em->getRepository(User::class)->findNameUsersTinydata($researchBar);// Filtre activé 
        $emails = $this->em->getRepository(User::class)->findEmailUsersTinydata($researchBar);// Filtre activé 
     //   $contents = $this->em->getRepository(ValueAttribute::class)->findValuesContentsTinydata($researchBar);// Contenus chelou
        $acquisitions = $this->em->getRepository(Acquisition::class)->findAcquisitionTinydata($researchBar); // Filtre activé
        $themes = $this->em->getRepository(Theme::class)->findThemesTinydata($researchBar); //Filtre activé
        $skills = $this->em->getRepository(Skill::class)->findSkillsTinydata($researchBar);// Filtre activé 
        $domains = $this->em->getRepository(Domain::class)->findDomainsTinydata($researchBar);// Filtre activé 
        $matrix = $this->em->getRepository(Matrix::class)->findMatrixTinydata($researchBar);// Filtre activé 

        $countCompanies = 0;
        $countUsers = 0;
        $countEmails = 0;
        $countFormations = 0;
        $countDomains = 0;
        $countSkills = 0;
        $countThemes = 0;
        $countAcquis = 0;
        $countContents = 0;

        /**
         * Ajout des companies
         */
        while (current($companies) && $countCompanies <= 5) {
            $returnValues[] = array(
                "objectId" => current($companies)['id'],
                "title" => current($companies)['name'],
                "type" => "ENTREPRISE");
            $countCompanies+=1;
            next($companies);
        }

        /**
         * Ajout des utilisateurs
         */

        while (current($users) && $countUsers <= 5) {
            $fullName = current($users)['firstName'] . ' ' . current($users)['lastName'];

            $returnValues[] = array(
                "objectId" => current($users)['id'],
                "title" => $fullName,
                "type" => "UTILISATEUR");
            $countUsers+=1;
            next($users);
        }


        /**
         * Ajout des emails
         */
        while (current($emails) && $countEmails <= 5) {
            $returnValues[] = array(
                "objectId" => current($emails)['id'],
                "title" => current($emails)['email'],
                "type" => "EMAIL");
            $countEmails+=1;
            next($emails);
        }


        /**
         * Ajout des Formations (matrices)
         * On récupere l'entité matrice
         */

        while (current($matrix)&& $countFormations <= 5) {

            $returnValues[] = array(
                "objectId" => $matrix[$countFormations]->getId(),
                "title" => $matrix[$countFormations]->getName(),
                "type" => "FORMATIONS");

            $countFormations+=1;
            next($matrix);
        }

        /**
         * Ajout des Domaines
         * On récupere l'entité domaine
         */
        while (current($domains)&& $countDomains <= 5) {
            $returnValues[] = array(
                "objectId" => $domains[$countDomains]->getId(),
                "title" => $domains[$countDomains]->getTitle(),
                "type" => "DOMAINE");
            $countDomains+=1;
            next($domains);
        }

        /**
         * Ajout des Compétences
         * On récupere l'entité compétence/skill
         */
        while (current($skills)&& $countSkills <= 5) {
            $returnValues[] = array(
                "objectId" => $skills[$countSkills]->getId(),
                "title" => $skills[$countSkills]->getTitle(),
                "type" => "COMPÉTENCE");
            $countSkills+=1;
            next($skills);
        }

        /**
         * Ajout des Thématiques
         */
        while (current($themes)&& $countThemes <= 5) {
            $returnValues[] = array(
                "objectId" => $themes[$countThemes]->getId(),
                "title" => $themes[$countThemes]->getTitle(),
                "type" => "THEME");
            $countThemes+=1;
            next($themes);
        }

        /**
         * Ajout des Acquis
         */
        // while (current($acquisitions)&& $countAcquis <= 5) {
        //     $returnValues[] = array(
        //         "objectId" => (int)current($acquisitions)['id'],
        //         "title" => current($acquisitions)['title'],
        //         "type" => "ACQUISITION");
        //     $countAcquis+=1;
        //     next($acquisitions);
        // }

        /**
         * Ajout des Contenus
         *
         * Attention, les contenus sont trop nombreux, il faut les trier car ça prend trop de temps dans la barre de recherche
         *
         */
        // while (current($contents)&& $countContents <= 5) {

        //     if(current($contents)['value'] !== "" && current($contents)['value'] !== null){
        //         $returnValues[] = array(
        //             "id" => current($contents)['id'],
        //             "title" => current($contents)['value'],
        //             "type" => "CONTENU");
        //         $countContents+=1;
        //     }

        //     next($contents);
        // }

        return $returnValues;

    }


    public function addValues($value, $typeValue, $title, $compteur){
        $compteur =0; //A REVOIR
        while (current($value) && $compteur <= 5) {
            //  dump($compteur . $typeValue);
            $returnValues[] = array(
                "id" => current($value)['id'],
                "title" => current($value)[$title],
                "type" => $typeValue);
            $compteur += 1;

            next($value);
        }
        return $returnValues;
    }




}
