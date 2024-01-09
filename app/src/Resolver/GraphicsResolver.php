<?php

namespace App\Resolver;

use App\Resolver\AbstractResolver;
use App\Service\Graphics\ConnectionsService;
use App\Service\Graphics\ContentsValidService;
use App\Service\Graphics\EmailOpenAccountsService;
use App\Service\Graphics\FeedbackService;
use App\Service\Graphics\GeneralProgressionService;
use App\Service\Graphics\OpenAccountsService;
use App\Service\Graphics\PerformanceService;
use App\Service\Graphics\RappelsMemorielsService;
use App\Service\Graphics\TimeSpentInLearningService;
use App\Service\Graphics\UserContributionsService;
use App\Service\StaticsData\ActiveUsersService;
use App\Service\StaticsData\TotalConnectionService;
use App\Service\StaticsData\TotalContentService;
use App\Service\StaticsData\TotalLearningTimeService;
use App\Service\StaticsData\TotalLearningTimeSpentInLearningService;

class GraphicsResolver extends AbstractResolver
{

    const MATRIX        = 'matrix';
    const DOMAIN        = 'domain';
    const SKILL         = 'skill';
    const THEME         = 'theme';
    const ACQUISITION   = 'acquisition';
    const CONTENT       = 'content';

    // Graphiques
    private $openAccountsService;
    private $feedbackService;
    private $contentsValidService;
    private $activeUsersService;
    private $totalLearningTimeService;
    private $totalContentService;
    private $connectionsService;
    private $totalConnectionService;
    private $timeSpentInLearningService;
    private $performanceService;
    private $userContributionsService;
    private $rappelsMemorielsService;

    public function __construct(
        EmailOpenAccountsService $emailOpenAccountsService,
        OpenAccountsService $openAccountsService,
        FeedbackService $feedbackService,
        ContentsValidService $contentsValidService,
        TotalLearningTimeSpentInLearningService $totalLearningTimeSpentInLearningService,
        GeneralProgressionService $generalProgressionService,
        ActiveUsersService $activeUsersService,
        TotalLearningTimeService $totalLearningTimeService,
        TotalContentService $totalContentService,
        ConnectionsService $connectionsService,
        TotalConnectionService $totalConnectionService,
        TimeSpentInLearningService $timeSpentInLearningService,
        PerformanceService $performanceService,
        UserContributionsService $userContributionsService,
        RappelsMemorielsService $rappelsMemorielsService
    ) {
        $this->openAccountsService = $openAccountsService;
        $this->emailOpenAccountsService = $emailOpenAccountsService;
        $this->feedbackService = $feedbackService;
        $this->contentsValidService = $contentsValidService;
        $this->activeUsersService = $activeUsersService;
        $this->totalLearningTimeService = $totalLearningTimeService;
        $this->totalContentService = $totalContentService;
        $this->connectionsService = $connectionsService;
        $this->totalConnectionService = $totalConnectionService;
        $this->timeSpentInLearningService = $timeSpentInLearningService;
        $this->performanceService = $performanceService;
        $this->userContributionsService = $userContributionsService;
        $this->rappelsMemorielsService = $rappelsMemorielsService;
    }

    /****************************************
     *          Fonctions générales         *
     ****************************************/

    /**
     * Sélectionne le fichier qui permet de récupérer les données du graphique sélectionné
     */
    public function getGraphData($args)
    {

        $filterType = $args['filterType'];
        $specificGraphData = $args['specificGraphData'];

        // Dirige vers le service en fonction du nom du graphique
        switch ($args['graphName']) {

                // PERFORMANCE - Check
            case 'performance':
                return $this->performanceService->getData($filterType, $specificGraphData);
                break;

                // CONNECTIONS - Check
            case 'connections':
                // return $this->connectionsService->getData($beginDate, $endDate, $userId, $companyIds, $groupedBy, $groupByUser);
                return $this->connectionsService->getData($filterType, $specificGraphData);
                break;

                // CONTENUS VALIDES - User View
            case 'contents_valid':
                return $this->contentsValidService->getData($filterType, $specificGraphData);
                break;

                // FEEDBACKS - Check
            case 'feedback':
                return $this->feedbackService->getData($filterType, $specificGraphData);
                break;

                // CONTRIBUTIONS DES APPRENANTS
            case 'user_contributions':
                return $this->userContributionsService->getData($filterType, $specificGraphData);
                break;

                // COMPTES OUVERTS
            case 'openaccounts':
                return $this->openAccountsService->getData($filterType, $specificGraphData);
                break;

                // TEMPS PASSÉ DANS L'APPRENTISSAGE - Chek
            case 'analytics_time':
                return $this->timeSpentInLearningService->getData($filterType, $specificGraphData);
                break;

            case 'rappels_memo':
                return $this->rappelsMemorielsService->getData($filterType, $specificGraphData);
                break;

            default:
                // Nothing to-do
                break;
        }
    }

    /**
     * Sélectionne le fichier qui permet de récupérer les données brutes se trouvant sur le dashboard
     */
    public function getRawData($args)
    {
        $filterType = $args['filterType'];
        $specificGraphData = $args['specificGraphData'];

        // Dirige vers le service en fonction du nom du graphique
        switch ($args['graphName']) {

                // Nombre total d'apprenants actifs
            case 'total_apprenants':
                return $this->activeUsersService->getData($filterType, $specificGraphData);
                break;

                // Nombre total de contenus échoués/validés - Check
            case 'total_contents_failed':
            case 'total_contents_valid':
            case 'total_contents':
                return $this->totalContentService->getData($filterType, $specificGraphData);
                break;

                // Nombre total de connexions - Check
            case 'total_connections':
                return $this->totalConnectionService->getData($filterType, $specificGraphData);
                break;

                // Temps total passé dans l'apprentissage
            case 'total_learning_time':
                // dd('test');
                return $this->totalLearningTimeService->getData($filterType, $specificGraphData);
                break;
        }


        return [];
    }
}
