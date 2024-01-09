<?php

namespace App\Service\Graphics;

use App\Entity\Content;
use App\Entity\User;
use App\Entity\UserContents;
use App\Model\GraphicsAbstract;
use Doctrine\Persistence\ManagerRegistry;

class PerformanceService extends GraphicsAbstract
{

    private $em;

    public function __construct(ManagerRegistry $mr)
    {
        $this->em = $mr->getManager("tinycoaching");
    }
    /**
     * UserID / nameDepth / DepthClickedId / filterFormationSelected
     */
    public function getData($args, $specificGraphData)
    {


        $args['filterFormationSelected'] = $specificGraphData['filterSelected'];
        $args['extended'] = false;

        $nbContentDone = $this->em->getRepository(UserContents::class)->getValidatedContentByGranularity($args, $specificGraphData);
        $nbContentByMatrix = $this->em->getRepository(Content::class)->getContentNumberByGranularity($args, $specificGraphData);
        $nbUsersByMatrix = $this->em->getRepository(User::class)->getNumberUserByMatrix($args, $specificGraphData);

        $result = [
            'dataGraphic' => [],
            'dataExtended' => []
        ];

        /* Données se trouvant sur le graphique */
        foreach ($nbContentDone as $contentDone) {
            foreach ($nbContentByMatrix as $contentByMatrix) {
                if ($contentDone['id'] === $contentByMatrix['id']) {
                    foreach ($nbUsersByMatrix as $usersByMatrix) {

                        $compareMatrixId = $specificGraphData['nameDepth'] === 'matrix' ? $contentDone['id'] : $contentDone['matrixID'];

                        if ($compareMatrixId === $usersByMatrix['id']) {
                            array_push($result['dataGraphic'], array(
                                "granularityID"             => $contentDone['id'],
                                "name"                      => $specificGraphData['nameDepth'] === 'matrix' ? $contentDone['name'] : $contentDone['title'],
                                "depth"                     => $specificGraphData['nameDepth'],
                                "value"                     => (int)ceil(($contentDone['total'] / ($contentByMatrix['total'] * $usersByMatrix['total'])) * 100),
                            ));
                        }
                    }
                }
            }
        }

        $args['extended'] = true;
        $nbContentDone = $this->em->getRepository(UserContents::class)->getValidatedContentByGranularity($args, $specificGraphData);

        /* Données détaillées */
        foreach ($nbContentDone as $contentDone) {
            foreach ($nbContentByMatrix as $contentByMatrix) {
                if ($contentDone['id'] === $contentByMatrix['id']) {

                    // Ajout dans les données détaillées
                    array_push($result['dataExtended'], array(
                        "companyName"       => $contentDone['companyName'],
                        "depth"             => $specificGraphData['nameDepth'],
                        "firstName"         => $contentDone['firstName'],
                        "lastName"          => $contentDone['lastName'],
                        "email"             => $contentDone['email'],
                        "granularityID"     => $contentDone['id'],
                        "name"              =>  $specificGraphData['nameDepth'] === 'matrix' ? $contentDone['name'] : $contentDone['title'],
                        "value"             => (int)ceil(($contentDone['total'] / $contentByMatrix['total']) * 100),
                    ));
                }
            }
        }

        return [
            'data' => json_encode(
                array(
                    "dataGraphic" => $result['dataGraphic'],
                    "dataExtended" => $result['dataExtended']
                )
            ) // Transformer le tableau en string pour le récupérer côté back
        ];

        return $result;
    }

    public function export($filters)
    {
        return [];
    }
}
