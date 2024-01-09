<?php

namespace App\GraphQL\Type\Request;

use Youshido\GraphQL\Type\Scalar\IntType;

class ThemeInt extends IntType
{

    private $middlewareService;

    public function getName()
    {
        return 'ThemeInt';
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
            $isValid = $this->middlewareService->checkThemeArg($value);
            if($isValid){
                return true;
            }
        }

        $this->middlewareService->throwError();
    }
}
