<?php

namespace App\Service;

use App\Entity\Acquisition;
use App\Entity\AnalyticsSkill;
use App\Entity\Company;
use App\Entity\Content;
use App\Entity\Matrix;
use App\Entity\Domain;
use App\Entity\Skill;
use App\Entity\SkillLevel;
use App\Entity\Level;
use App\Entity\User;
use App\Repository\SkillRepository;
use Doctrine\ORM\EntityManagerInterface;

class AcquisitionService
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
    * Graphique : PROGRESSION GENERALE - Se base sur la table de statistique
    * Déterminer le nombre d'acquis validé en fonction du nombre d'acquis total.
    */
    
    public function getUserGeneralProgression($args){

        // Repository de tous les éléments nécessaires 
        $userRepo =$this->em->getRepository(User::class);
        $companyRepo = $this->em->getRepository(Company::class);
        $contentRepo = $this->em->getRepository(Content::class);
        $analyticsRepo = $this->em->getRepository(AnalyticsSkill::class);

        // Obtenir l'entreprise dans laquelle il se trouve
        $userCompaniesId = $userRepo->getUserCompanyID($args['userId'])[0]["id"];

        // Obtenir toutes les formations de l'entreprise
        $matrixIds = $companyRepo->getFormationsCompanies($userCompaniesId, true);

        // Nombre d'acquis au total par Matrice et nombre total d'acquis validés par l'utilisateur
        $nbAcquis = $contentRepo->countAcquisByMatrix($matrixIds, $args);

        $nbAcquisValidated = $analyticsRepo->countAcquisValidatedByUser($matrixIds, $args);
        
        $totalAcquisValidated = 0;
        $totalAcquis = 0;

        // Boucle qui permet de déterminer le nombre d'acquis et le nombre d'acquis validés par l'utilisateur
        foreach($nbAcquis as $keyA => $acquis){
            foreach($nbAcquisValidated as $keyB => $acquisValidated){
                if($acquis['matrixId'] === $acquisValidated['matrixId']){
                    
                    $totalAcquis += $acquis['acquisTotal'];
                    $totalAcquisValidated += $acquisValidated['totalAcquisValidated'];
                }
            }
        }
    
        if($totalAcquis === 0){
            $userScore = 0;
        } else {
            $userScore = (int)(round(($totalAcquisValidated / $totalAcquis) * 100));
        }

        $result = array(
            "totalValidated" => $totalAcquisValidated,
            "totalNum" => $totalAcquis,
            "userScore" => $userScore,
            "goalScore" => 100
        );

        return $result;
    }

    // // CALCUL DE LA PROGRESSION GÉNÉRALE DE L'UTILISATEUR
    // public function getUserGeneralProgression($args){
    //     $userRepo =$this->em->getRepository(User::class);

    //     $userEntity = $userRepo->findOneBy(array("id" => $args['idUser']));
    //     $company = $userEntity->getCompany(); // companyId from userIdId
    //     $matrices = $company->getMatrices();

    //     $totalValid = 0;
    //     $totalNum = 0;

    //     foreach($matrices as $matrix){
    //         $matrixProgress = $this->getNumberAcquisValidatedByMatrix($userEntity, $matrix);
    //         $totalValid += $matrixProgress['validAcquis'];
    //         $totalNum += $matrixProgress['numAcquis'];  
    //     }
    
    //     $result = array(
    //         "totalValidated" => $totalValid,
    //         "totalNum" => $totalNum,
    //         "userScore" => (int)(round(($totalValid / $totalNum) * 100)),
    //         "goalScore" => 100
    //     );

    //     return $result;
    // }
    
    /**
     * Get the number of acquisition in the matrix and the number of acquisition validated by the user
     */
    public function getNumberAcquisValidatedByMatrix(User $user, Matrix $matrix)
    {
        $result = array("numAcquis" => 0, "validAcquis" => 0);
        $domains = $this->em->getRepository(Domain::class)->findBy(["matrix"=>$matrix]);

        foreach($domains as $domain)
        {
            $domainResult = $this->getNumberAcquisValidatedByDomain($user, $domain);
            $result["numAcquis"] += $domainResult["numAcquis"];
            $result["validAcquis"] += $domainResult["validAcquis"];
        }

        return $result;
    }

    /**
     * Get the number of acquisition in the domain and the number of acquisition validated by the user
     */
    public function getNumberAcquisValidatedByDomain(User $user, Domain $domain)
    {
        $result = array("numAcquis" => 0, "validAcquis" => 0);
        $skills = $this->em->getRepository(Skill::class)->findBy(["domain"=>$domain]);

        foreach($skills as $skill)
        {
            $skillResult = $this->getNumberAcquisValidatedBySkill($user, $skill);
            $result["numAcquis"] += $skillResult["numAcquis"];
            $result["validAcquis"] += $skillResult["validAcquis"];
        }

        return $result;
    }

    /**
     * Get the number of acquisition in the skill and the number of acquisition validated by the user
     */
    public function getNumberAcquisValidatedBySkill(User $user, Skill $skill)
    {
        $levelRepo = $this->em->getRepository(Level::class);
        $acquisRepo = $this->em->getRepository(Acquisition::class);
        $skillRepo = $this->em->getRepository(Skill::class);

        $result = array("numAcquis" => 0, "validAcquis" => 0);
        $skillLevels = $this->em->getRepository(SkillLevel::class)->findBy(["skill"=>$skill]);
        $userLevel = $levelRepo->getUserLevel($skill, $user->getId());
        if(!$userLevel){
            $userLevel = $skillRepo->getSkillMinLevel($skill);
        }

        foreach($skillLevels as $skillLevel)
        {
            $skillAcquis = $acquisRepo->getCountAcquisFromSkill($skill, $skillLevel->getLevel());
            
            //If the user rank is upper than the skill rank -> we consider that the user has validated all acquisition in this level
            if($userLevel->getRank() > $skillLevel->getLevel()->getRank()){
                $validAcquis = $skillAcquis;
            }
            else{
                $validAcquis = $user->getUserClient()->getValidAcquis($skill, $skillLevel->getLevel());
            }

            $result["numAcquis"] += count($skillAcquis);
            $result["validAcquis"] += count($validAcquis);
        }

        return $result;
    }

    

}
