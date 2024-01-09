<?php

namespace App\Resolver;

use App\Entity\Company;
use App\Entity\Content;
use App\Entity\Domain;
use App\Entity\Matrix;
use App\Entity\Skill;
use App\Entity\Theme;
use App\Entity\User;
use App\Entity\UserContents;
use App\Service\DateService;
use App\Service\GlobalFilterService;
use App\Service\UserService;

class GranularitiesResolver extends AbstractResolver
{

    const MATRIX = 'matrix';
    const DOMAIN = 'domain';
    const SKILL = 'skill';
    const THEME = 'theme';
    const ACQUISITION = 'acquisition';
    const CONTENT = 'content';

    /** 
     * @var GlobalFilterService
     **/
    private $filterService;

    public function __construct(UserService $userService, DateService $dateService, GlobalFilterService $filterService)
    {
        $this->userService = $userService;
        $this->dateService = $dateService;
        $this->filterService = $filterService;
    }


    public function totalGranularities($companies)
    {

        $matrixes = $this->em->getRepository(Matrix::class)->totalMatrixes($companies);

        return array("total" => $matrixes[0][1]);
    }

    /**
     * Permet de créer le filtre des formations
     *
     * @param $id
     * @param $depth
     * @return mixed
     */
    public function getGranularities($companies, $matrixFilter, $userId)
    {
        $matrixes = $this->em->getRepository(User::class)->getAllMatrixes($companies, $matrixFilter, $userId);
        $domains  = $this->em->getRepository(Domain::class)->getAllDomains();
        $skills   = $this->em->getRepository(Skill::class)->getAllSkills();
        $themes   = $this->em->getRepository(Theme::class)->getAllThemes();

        foreach ($themes as $theme) {
            foreach ($skills as $key => $skill) {
                if ($skill['id'] === $theme['skillId']) {
                    if (!array_key_exists('themes', $skill)) {
                        $skills[$key]['themes'] = array();
                    }
                    array_push($skills[$key]['themes'], $theme);
                    break;
                }
            }
        }

        foreach ($skills as $skill) {
            foreach ($domains as $key => $domain) {
                if ($domain['id'] === $skill['domainId']) {
                    if (!array_key_exists('skills', $domain)) {
                        $domains[$key]['skills'] = array();
                    }
                    array_push($domains[$key]['skills'], $skill);
                    break;
                }
            }
        }

        foreach ($domains as $domain) {
            foreach ($matrixes as $key => $matrix) {
                if ($matrix['id'] === $domain['matrixId']) {
                    if (!array_key_exists('domains', $matrix)) {
                        $matrixes[$key]['domains'] = array();
                    }
                    array_push($matrixes[$key]['domains'], $domain);
                    break;
                }
            }
        }

        return $matrixes;
    }

}
