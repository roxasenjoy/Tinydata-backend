<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class UserSessionService
{

    private $em;
    public const MAX_INTENTION_TIME = 300;  //5mn 
    public const MAX_CONTENT_TIME = 1200;   //20mn
    public const SPECIAL_CONTENT_TYPE = "content";


    public const VALID_TYPES = [
        "welcome",
        "culture",
        "matrix_choice",
        "domaine_choice",
        "content",
        "quiz",
        "quiz_validation",
        "semantical_search",
        "semantical_search_result",
        "semantical_search_no_result",
        "semantical_bad_contents_proposition",
        "citation",
        "goodbye"
    ];

    public const TC_TEXT_CORRESPONDANCE = array(
        "welcome"                               => "welcome",
        "culture"                               => "culture",
        "matrix_choice"                         => "matrix_choice",
        "domaine_proposition"                   => "domaine_choice",
        "pre_content"                           => "content",
        "quiz"                                  => "quiz",
        "validation"                            => "quiz_validation",
        "pre_semantical_search"                 => "semantical_search",
        "semantical_search_result"              => "semantical_search_result",
        "semantical_search_no_result"           => "semantical_search_no_result",
        "semantical_bad_contents_proposition"   => "semantical_bad_contents_proposition",
        "citation"                              => "citation",
        "goodbye"                               => "goodbye"
    );


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
}
