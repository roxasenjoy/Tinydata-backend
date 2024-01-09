<?php

namespace App\Resolver;

use App\Entity\User;
use App\Entity\Stats;


class StatsResolver extends AbstractResolver
{



    public function __construct()
    {

    }

    public function getStats()
    {
        $user = $this->em->getRepository(User::class)->findOneBy(["id" => 3727]);
        $stats = $this->em->getRepository(Stats::class)->findBy(["user" => 3727]);

        return array("user"=>$user, "data1"=>$stats[0]->getData1(),"data2"=>$stats[0]->getData2());
    }

}
