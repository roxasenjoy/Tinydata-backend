<?php
/**
 * User: LucasG
 * Date: 11/06/21
 */

namespace App\Exception;

use InvalidArgumentException;

class ApiProblem
{
    const INVALID_ACCESS_TOKEN = 1001;
    const INSCRITPION_REQUIRED = 1002;
    const MISSING_ARGUMENTS = 1003;
    const MISSING_WELCOME_SURVEY = 1004;
    const INVALID_FORM_DATA = 1005;
    const EMAIL_TAKEN = 1006;
    const INVALID_TIMEZONE = 1007;
    const AUTH_API_CONNEXION = 1008;
    const AUTH_API_RESPONSE_TYPE = 1009;
    const FORBIDDEN = 1010;
    const SSO_COMMUNICATION_FAILED = 1011;
    const UNAUTHORIZED = 401;
    const AUTHORIZATION_DONT_EXIST = 1013;
    const ALREADY_EXISTS = 1014;

    private static $titles = array(
        self::INVALID_ACCESS_TOKEN      => 'Token not exist or invalid',
        self::INSCRITPION_REQUIRED      => 'Inscription required',
        self::MISSING_ARGUMENTS         => 'Veuillez remplir tous les champs obligatoires',
        self::MISSING_WELCOME_SURVEY    => 'Welcome survey not exist',
        self::INVALID_FORM_DATA         => 'Invalid form submitted data',
        self::EMAIL_TAKEN               => 'This email is taken',
        self::INVALID_TIMEZONE          => 'Invalid timezone ID',
        self::AUTH_API_CONNEXION        => 'Une erreur est survenue lors de la connexion à l\'API d\'authentification',
        self::AUTH_API_RESPONSE_TYPE    => 'La réponse du serveur d\'authentification n\'est pas dans un format convenable pour être traitée par le serveur',
        self::FORBIDDEN                 => 'Forbidden access to this data',
        self::SSO_COMMUNICATION_FAILED  => 'SSO_COMMUNICATION_FAILED',
        self::UNAUTHORIZED              => 'Unauthorized',
        self::AUTHORIZATION_DONT_EXIST  => 'The asked privilege doesn\'t exists',
        self::ALREADY_EXISTS            => 'Already exists',

    );

    private $statusCode;

    private $type;

    private $title;

    private $extraData = array();

    public function __construct($statusCode, $type)
    {
        $this->statusCode = $statusCode;
        $this->type = $type;

        if (!isset(static::$titles[$type])) {
            throw new InvalidArgumentException('No title for type '.$type);
        }

        $this->title = static::$titles[$type];
    }

    public function toArray()
    {
        return array_merge(
            $this->extraData,
            array( "errors" => array(
                'status' => $this->statusCode,
                'type' => $this->type,
                'title' => $this->title,
            )
            )
        );
    }

    public function set($name, $value)
    {
        $this->extraData[$name] = $value;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getType()
    {
        return $this->type;
    }
}
