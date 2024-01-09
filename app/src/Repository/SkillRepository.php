<?php

namespace App\Repository;

use App\Entity\Skill;
use App\Entity\SkillLevel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Service\GlobalFilterService;

/**
 * @method Skill|null find($id, $lockMode = null, $lockVersion = null)
 * @method Skill|null findOneBy(array $criteria, array $orderBy = null)
 * @method Skill[]    findAll()
 * @method Skill[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SkillRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, GlobalFilterService $filterService)
    {
        parent::__construct($registry, Skill::class);
        $this->filterService = $filterService;
    }

    public function findSkillsTinydata($researchBar){

        $filterCompany = $this->filterService->getAllCompaniesForUser();

        $qb = $this->createQueryBuilder('skill')
        ->join('skill.domain', 'domain')
        ->join('domain.matrix', 'matrix')
        ->join('matrix.companies', 'company');

        $qb->where("skill.title like :researchBar")
        ->setParameter(':researchBar', '%'.$researchBar.'%');

        if($filterCompany){
            $qb->andWhere('company.id IN (:companyList)')
            ->setParameter(':companyList', $filterCompany);
        }

        $qb->groupBy('skill.id');
        $qb->orderBy('skill.title');

        return $qb->getQuery()->getResult();

    }

	public function getAllSkills(){
        $companyFilter = $this->filterService->getAllCompaniesForUser();
                    
        $skillQuery = $this->createQueryBuilder('skill')
            ->select("skill.id, skill.title, domain.id as domainId, matrix.id as matrixId")
            ->distinct(true)
            ->leftJoin('skill.domain', 'domain')
            ->leftJoin('domain.matrix', 'matrix')
            ->leftJoin('matrix.companies', 'company')
            ->orderBy('skill.title');
		if($companyFilter){
            $skillQuery->where("company.id IN (:companyList)")
            ->setParameter('companyList', $companyFilter);
		}
        $skills = $skillQuery->getQuery()->getResult();
        return $skills;
    }

    public function getSkillMinLevel(?Skill $s){
        return $this->getSkillLevel($s, false);
    }

    private function getSkillLevel(?Skill $s, $isUpper){
        $maxLevel = null;
        if(!$s){
            $maxLevel = $this->_em->getRepository(Level::class)->findOneBy(["rank" => 1]);
        }
        else{
            $skillLevels = $this->_em->getRepository(SkillLevel::class)->findBy(['skill' => $s]);
            foreach($skillLevels as $skillLevel){
                if($isUpper && (!$maxLevel || $maxLevel->getRank() < $skillLevel->getLevel()->getRank()))
                {
                    $maxLevel = $skillLevel->getLevel();
                }
                else if(!$isUpper && (!$maxLevel || $maxLevel->getRank() > $skillLevel->getLevel()->getRank()))
                {
                    $maxLevel = $skillLevel->getLevel();
                }
            }
        }

        return $maxLevel;
    }
}
