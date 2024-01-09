<?php

namespace App\GraphQL\Type\Request;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Youshido\GraphQL\Type\Scalar\IntType;

class MatrixInt extends IntType
{

    private $middlewareService;

    public function getName()
    {
        return 'MatrixInt';
    }

    public function isValidValue($value)
    {
        global $kernel;
        $this->container = $kernel->getContainer();
        $this->middlewareService = $this->container->get('MiddlewareService');
        
        if(is_null($value)){
            return true;
        }
        else if(is_int($value)){
            $isValid = $this->middlewareService->checkMatrixArg($value);
            if($isValid){
                return true;
            }
        }

        $this->middlewareService->throwError();
    }
}
