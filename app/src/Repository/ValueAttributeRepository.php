<?php

namespace App\Repository;

use App\Entity\ValueAttribute;
use App\Service\GlobalFilterService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ValueAttribute|null find($id, $lockMode = null, $lockVersion = null)
 * @method ValueAttribute|null findOneBy(array $criteria, array $orderBy = null)
 * @method ValueAttribute[]    findAll()
 * @method ValueAttribute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ValueAttributeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, GlobalFilterService $filterService)
    {
        parent::__construct($registry, ValueAttribute::class);
        $this->filterService = $filterService;
    }

    public function findValuesContentsTinydata($researchBar){
        return $this->createQueryBuilder('valueContent')
            ->select('valueContent.id, valueContent.value')
            ->andWhere('valueContent.value LIKE :searchTerm')
            ->setParameter('searchTerm',  '%'.$researchBar.'%')
            ->getQuery()
            ->getResult();
    }

    public function getTotalLearningTime( 
        $beginDate, 
        $endDate, 
        $matrixFilter, 
        $domainFilter, 
        $skillFilter, 
        $themeFilter,
        $companyId,
        $userTotalLearningTime,
        $idUser){

        $where = "";
        if($userTotalLearningTime === true){
            $where = "user.id = $idUser";
        } else {
            if($companyId && sizeof($companyId) == 0){
                $companyId = null;
            }
                
            if($companyId){
                $companyId = implode("," ,$companyId);
            }
            else{
                $companyId = $this->filterService->getAllCompaniesForUser();
                if($companyId){
                    $companyId = implode(",", $companyId);
                }
            }
            if($companyId){ // Null si SA
                $where = "user_client.company_id IN ($companyId)";
            } else {
                $where = "1";
            }
        }

        // Le attribute_id = 5 récupere le temps de chaque contenus 
        $subReq = "(SELECT value FROM value_attribute WHERE attribute_id = 5 AND content_id = user_contents.content_id)";

        $sql = "SELECT content_id, COUNT(*) as rnb_realisedr, $subReq as time, $subReq*COUNT(*) as 'total'
        FROM
            user_contents
                JOIN
            content ON (content.id = user_contents.content_id)
                JOIN
            user_client ON (user_client.id = user_contents.user_client_id)
                JOIN
            user ON user.user_client_id = user_client.id
                JOIN
            acquisition ON (content.acquisition_id = acquisition.id)
                JOIN
            skill_level ON (acquisition.skill_level_id = skill_level.id)
                JOIN
            level ON (level.id = skill_level.level_id)
                
                JOIN
            skill ON (skill_level.skill_id = skill.id)
                JOIN
            domain ON (skill.domain_id = domain.id)
                JOIN
            matrix ON (domain.matrix_id = matrix.id)
        WHERE 
            $where
            AND user_contents.date BETWEEN '$beginDate' AND '$endDate'
            AND matrix.id IN ($matrixFilter)
            AND domain.id IN ($domainFilter)
            AND skill.id IN ($skillFilter)
            
        GROUP BY content_id;";

        $conn = $this->getEntityManager()->getConnection();
        $sql = $conn->prepare($sql);
        $sql->executeQuery();
        $sql = $sql->fetchAll();

        $sum = 0;
        foreach($sql as $value){
            $sum += $value['total'];
        }

        return $sum;

    }
}
