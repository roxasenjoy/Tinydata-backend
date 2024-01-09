<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class ApiException
 * User: LucasG
 * Date: 11/06/21
 */
class ApiProblemException extends HttpException
{
    private $apiProblem;
    public function __construct(
        ApiProblem $apiProblem,
        \Exception $previous = null,
        array $headers = array(),
        $code = 0
    ) {
        $this->apiProblem = $apiProblem;
        $statusCode = $apiProblem->getStatusCode();
        $message = $apiProblem->getTitle();

        parent::__construct($statusCode, $message, $previous, $headers, $apiProblem->getType());
    }
    public function getApiProblem()
    {
        return $this->apiProblem;
    }
}
