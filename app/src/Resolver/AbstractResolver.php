<?php

namespace App\Resolver;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

abstract class AbstractResolver
{

    /**
     * @var  EntityManagerInterface
     */
    protected $em;

    public function init(EntityManagerInterface $em, ManagerRegistry $mr)
    {
        $this->em = $mr->getManager("tinycoaching");
    }

    protected function createNotFoundException($message = 'Entity not found')
    {
        return new \Exception($message, 404);
    }

    protected function createInvalidParamsException($message = 'Invalid params')
    {
        return new \Exception($message, 400);
    }

    protected function createAccessDeniedException($message = 'No access to this action')
    {
        return new \Exception($message, 403);
    }
}
