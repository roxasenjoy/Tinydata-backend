<?php
/**
 * Created by PhpStorm.
 * User: inesm
 * Date: 09/05/18
 * Time: 16:13
 */

namespace App\Entity\Constants;

use App\Entity\ImportHistory;

/**
 * Class TinyCoach
 * @package App\Entity\Constants
 */
class TinyCoach
{
    const TINY_MIN_SCORE = 128;

    const HISTORY_TYPE = array(
        ImportHistory::IMPORT_TYPE_TCCONTENT => [
            "status",
            "date",
            "username",
            "usermail",
            "rank",
            "filename",
        ],
        ImportHistory::IMPORT_TYPE_ONBOARDING => [
            'status',
            'date',
            'username',
            'usermail',
            'filename',
            'languageString'
        ]
    );
    const TC_CONENT_EXCLUDE_HEADER_SITUATION = array(
        "Situations",
        "Situation",
    );
    const TC_CONENT_EXCLUDE_HEADER_PREREQUIS = array(
        "champs de connaissances/prérequis",
        "champs de connaissances /prérequis"
    );

    const TC_CONTENT_ATTRIBUTES = array(
        "CONTENT001" => array(
            "name" => "Contenu",
            "short_title" => "title1",
            "type" => "string",
            "order" => 1
        ),
        "CONTENT002" => array(
            "name" => "Sous-titre du contenu",
            "short_title" => "catchy_phrase",
            "type" => "text",
            "order" => 2
        ),
        "CONTENT003" => array(
            "name" => "Type du contenu",
            "short_title" => "content_type",
            "type" => "string",
            "order" => 3
        ),
        "CONTENT004" => array(
            "name" => "Lien du contenu",
            "short_title" => "content_url",
            "type" => "string",
            "order" => 4
        ),
        "CONTENT005" => array(
            "name" => "Image",
            "short_title" => "picture",
            "type" => "string",
            "order" => 5
        ),
        "CONTENT006" => array(
            "name" => "Temps de lecture/écoute",
            "short_title" => "length",
            "type" => "string",
            "order" => 6
        ),
        "CONTENT007" => array(
            "name" => "Point info",
            "short_title" => "digital_culture",
            "type" => "text",
            "order" => 7
        ),
        "CONTENT008" => array(
            "name" => "Citation",
            "short_title" => "quote",
            "type" => "text",
            "order" => 8
        ),
        "CONTENT009" => array(
            "name" => "Auteur citation",
            "short_title" => "quote_author",
            "type" => "text",
            "order" => 9
        ),
        "CONTENT010" => array(
            "name" => "Resource id (scorm)",
            "short_title" => "ressource_id",
            "type" => "scorm",
            "order" => 10
        ),
        "CONTENT011" => array(
            "name" => "Secret Key (scorm)",
            "short_title" => "secret_key",
            "type" => "scorm",
            "order" => 11
        ),
        "CONTENT012" => array(
            "name" => "Brouillon",
            "short_title" => "brouillon",
            "type" => "xchoice",
            "order" => 12
        ),
    );

    const GENDER_TYPE_MAN = 1;
    const GENDER_TYPE_WOMAN = 2;
    const GENDER_TYPE_NEUTRE = 3;

    const AGE_TYPE_19 = 1;
    const AGE_TYPE_20 = 2;
    const AGE_TYPE_30 = 3;
    const AGE_TYPE_40 = 4;
    const AGE_TYPE_50 = 5;
    const AGE_TYPE_60 = 6;
    const AGE_TYPE_70 = 7;

    const HOLD_CGU = 0;
    const ACCEPT_CGU = 1;

    const AGE_TYPE = array(
        self::AGE_TYPE_19 => ["Moins de 19", "Under 19"],
        self::AGE_TYPE_20 => ["20-29"],
        self::AGE_TYPE_30 => ["30-39"],
        self::AGE_TYPE_40 => ["40-49"],
        self::AGE_TYPE_50 => ["50-59"],
        self::AGE_TYPE_60 => ["60-69+"],
    );

    const GENDER_TYPE = array(
        self::GENDER_TYPE_MAN => ['Homme', 'Au masculin', 'Men', 'As a man'],
        self::GENDER_TYPE_WOMAN => ['Femme', 'Au féminin', 'Women', 'As a woman'],
        self::GENDER_TYPE_NEUTRE => ['Neutre', 'Neutral', 'Other'],
    );

    const CGU_TYPE = array(
        self::HOLD_CGU => ['Non', 'Plus de détails', 'No', 'More details'],
        self::ACCEPT_CGU => ['Oui', 'Yes']
    );

    const NOTIF_TYPE = array(
        self::HOLD_CGU => ['Non', 'Plus de détails', 'No', 'More details'],
        self::ACCEPT_CGU => ['Oui', 'Yes']
    );

    const POSITIVE_REPLY = 1;
    const NEGATIVE_REPLY = 2;
    const NOT_APPLIED_REPLY = 3;

    const GOOD_POSITIVE_REPLY = 1;
    const EASY_NEGATIVE_REPLY = 2;
    const LONG_NEGATIVE_REPLY = 3;
    const DIFFICULT_NEGATIVE_REPLY = 4;

    const FEEDBACK_QUESTON =
        "Avez-vous apprécié le contenu ? 
        Votre réponse me permettra de mieux vous épauler tout au long de notre parcours.";

    const FEEDBACK_RESPONSES = [
        [
            "id" => "resp1",
            "value" => "Bien",
            "type" => self::GOOD_POSITIVE_REPLY
        ],
        [
            "id" => "resp2",
            "value" => "Trop facile",
            "type" => self::EASY_NEGATIVE_REPLY
        ],
        [
            "id" => "resp3",
            "value" => "Trop long",
            "type" => self::LONG_NEGATIVE_REPLY
        ],
        [
            "id" => "resp4",
            "value" => "Un peu difficile",
            "type" => self::DIFFICULT_NEGATIVE_REPLY
        ],
    ];

    const CONTENT_TYPES = [
        'article'   => "Article",
        'gaming'    => "Gaming",
        'bd'        => "BD",
        'scorm'     => "Scorm",
        'video'     => "Vidéo",
        'media'     => "Multimédia",
        'audio'     => "Podcast"
    ];
    const CONTENT_TYPE_VIDEO    = 'video';
    const CONTENT_TYPE_AUDIO    = 'audio';
    const CONTENT_TYPE_MEDIA    = 'media';
    const CONTENT_TYPE_ARTICLE  = 'article';
    const CONTENT_TYPE_BD       = 'bd';
    const CONTENT_TYPE_GAMING   = 'gaming';
    const CONTENT_TYPE_SCORM    = 'scorm';

    const CONTENT_LESS_THAN_ONE_MINUTE = 1;
    const CONTENT_BETWEEN_ONE_THREE_MINUTE = 2;
    const CONTENT_BETWEEN_THREE_FIVE_MINUTE = 3;
    const CONTENT_MORE_THAN_FIVE_MINUTE = 4;

    const ON_BOARDING_BEGIN = 1;
    const ON_CONTENT_BEGIN = 2;

    /* Temps total passé dans l’apprentissage entre clic sur bouton
    “Commencer” et “Validation quiz” lors du dernier quiz validé */
    const ANALYTIC1 = 1;
    /* Le temps entre "Commencer" (1e TC Texte) et le moment où la personne clique sur "Valider" son quiz */
    const ANALYTIC2 = 2;

    const ALL_SKILL_MAXI = 'Maxi';

    const TINY_MATRIX = 1;
    const EXECUTIVE_MATRIX = 2;
    const PASSWORD_CREATED = 'Votre mot de passe a bien été créé !';
    const PASSWORD_RESET = 'Votre mot de passe a été réinitialisé.';

    const TC_CONTENT_COLUMNS_INDEXES = [
        0  => 'ID',
        1  => 'Domaine',
        2  => 'Compétence',
        3  => 'Thème',
        4  => 'Acquis',
        5  => 'Acquis Essentiel',
        6  => 'Ordre de priorité',
        7  => 'Contenu',
        8  => 'Sus-titre du contenu',
        9 => 'Type du contenu',
        10 => 'Lien du contenu',
        11  => 'Image',
        12 => 'Temps de lecture/écoute',
        13 => 'Point info',
        14 => 'Citation',
        15 => 'Auteur citation',
        16 => "ressource id",
        17 => "Secret Key",
        18 => 'Question quiz',
        19 => 'Réponse : Vrai',
        20 => 'Réponse : Faux',
        21 => 'Feedback Quiz',
        22 => 'Question rappel mémoriel',
        23 => 'Rappel mémoriel : définition',
        24 => 'Brouillon',
        25 => 'terminé',
        26 => 'Commentaires + relecture',
        27 => 'Modifié(e) par :',
        28 => 'Date de modification'
    ];

    const INIT_TC_CONTENT =  [
        self::TC_CONTENT_COLUMNS_INDEXES[0] => "",
        self::TC_CONTENT_COLUMNS_INDEXES[1] => "",
        self::TC_CONTENT_COLUMNS_INDEXES[2] => "",
        self::TC_CONTENT_COLUMNS_INDEXES[3] => "",
        self::TC_CONTENT_COLUMNS_INDEXES[4] => "",
        self::TC_CONTENT_COLUMNS_INDEXES[5] => "",
        self::TC_CONTENT_COLUMNS_INDEXES[6] => "",
        self::TC_CONTENT_COLUMNS_INDEXES[7] => "",
        self::TC_CONTENT_COLUMNS_INDEXES[8] => "",
        self::TC_CONTENT_COLUMNS_INDEXES[9] => "",
        self::TC_CONTENT_COLUMNS_INDEXES[10] => "",
        self::TC_CONTENT_COLUMNS_INDEXES[11] => "",
        self::TC_CONTENT_COLUMNS_INDEXES[12] => "",
        self::TC_CONTENT_COLUMNS_INDEXES[13] => "",
        self::TC_CONTENT_COLUMNS_INDEXES[14] => "",
        self::TC_CONTENT_COLUMNS_INDEXES[15] => "",
        self::TC_CONTENT_COLUMNS_INDEXES[16] => "",
        self::TC_CONTENT_COLUMNS_INDEXES[17] => "",
        self::TC_CONTENT_COLUMNS_INDEXES[18] => "",
        self::TC_CONTENT_COLUMNS_INDEXES[19] => "",
        self::TC_CONTENT_COLUMNS_INDEXES[20] => "",
        self::TC_CONTENT_COLUMNS_INDEXES[21] => "",
        self::TC_CONTENT_COLUMNS_INDEXES[22] => "",
        self::TC_CONTENT_COLUMNS_INDEXES[23] => "",
        self::TC_CONTENT_COLUMNS_INDEXES[24] => "",
        self::TC_CONTENT_COLUMNS_INDEXES[25] => "",
        self::TC_CONTENT_COLUMNS_INDEXES[26] => "",
        self::TC_CONTENT_COLUMNS_INDEXES[27] => "",
        self::TC_CONTENT_COLUMNS_INDEXES[28] => ""
    ];

    // USER CLIENT STATUS
    const USER_STATUS_MAIL_SENT = "MAIL_SENT";
    const USER_STATUS_ONBOARDED = "ONBOARDED";
    const USER_STATUS_SIGNED_UP = "SIGNED_UP";
    const USER_STATUS_EXPIRED_CONTRACT = "EXPIRED_CONTRACT";
    const USER_STATUS_NEVER_CONNECTED = "NEVER_CONNECTED";


    const STATUSES_ARRAY = array(
        self::USER_STATUS_MAIL_SENT,
        self::USER_STATUS_ONBOARDED,
        self::USER_STATUS_SIGNED_UP,
        self::USER_STATUS_EXPIRED_CONTRACT,
        self::USER_STATUS_NEVER_CONNECTED
    );
    // USER CLIENT STATE (ÉTAT)
    const USER_STATE_ACTIVE = "ACTIVE";
    const USER_STATE_INACTIVE = "INACTIVE";
    const USER_STATE_DEFAULT = "DEFAULT_VALUE";

    // OPERATION TYPES (ADMIN STUDENTS DASHBOARD)
    const OP_DELETE = "OP_DELETE";
    const OP_TRANSFER = "OP_TRANSFER";
    const OP_ACTIVATE = "OP_ACTIVATE";
    const OP_DEACTIVATE = "OP_DEACTIVATE";
}
