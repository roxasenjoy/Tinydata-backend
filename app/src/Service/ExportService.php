<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\Matrix;
use App\Entity\User;
use App\Resolver\GraphicsResolver;
use Doctrine\Persistence\ManagerRegistry;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer as Writer;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use stdClass;

/**
 * @AndrewMahe
 * Class ExportService
 * This service is here to prepare export EXCEL & CSV
 * @package App\Service
 */
class ExportService
{

    // Modification du nom des clés pour l'export EXCEL / CSV
    const exportData = [
        'performance' => [ // GRAPHIC
            'depth' => 'Type de granularité',
            'name' => 'Titre de la granularité',
            'companyName' => 'Groupe',
            'firstName' => 'Prénom',
            'lastName' => 'Nom',
            'email' => 'Adresse email',
            'value' => '% Acquis validés'
        ],
        'feedback' => [ // GRAPHIC
            'depth' => 'Type de granularité',
            'name' => 'Titre de la granularité',
            'feedbackName' => 'Feedback',
            'percentage' => 'Pourcentage'
        ],
        'analytics_time' => [
            'firstName' => 'Prénom',
            'lastName'  => 'Nom',
            'email' => 'Adresse email',
            'time' => "Temps d'apprentissage"
        ],
        'total_apprenants' => [ // STATIC
            'groupePrincipal' => 'Groupe principal',
            'groupe' => 'Groupe',
            'firstName' => 'Prénom',
            'lastName' => 'Nom',
            'email' => 'Adresse email',
            'lastConnection' => "État"
        ],
        'connections' => [
            "companyName" => "Groupe",
            "userFirstName" => "Prénom",
            "userLastName" => "Nom",
            "userEmail" => 'Adresse email',
            'date' => 'Date',
            "count" => "Nombre de connexions pendant le mois",
            'totalCount' => "Nombre de connexions pendant la période filtrée",
        ],
        'feedback' => [
            'depth' => 'Type de granularité',
            'name' => 'Titre de la granularité',
            'feedbackName' => 'Feedback',
            'percentage' => 'Pourcentage'
        ],
        'user_contributions' => [
            'message' => 'Commentaire',
            'groupePrincipal' => 'Groupe principal',
            'groupe' => 'Groupe',
            'firstName' => 'Prénom',
            'lastName' => 'Nom',
            'email' => 'Adresse email',
            'nameMatrix' => 'Formation',
            'nameDomain' => 'Domaine',
            'nameSkill' => 'Compétence',
            'nameTheme' => 'Thème',
            'nameAcquis' => 'Acquis',
            'nameContent' => 'Contenu'
        ],
        'openaccounts' => [
            "roles" => "Produit",
            "organisation" => "Groupe Principal",
            "sousOrganisation" => "Groupe",
            "email" => 'Adresse email',
            "firstName" => "Prénom",
            "lastName" => "Nom",
            "formation" => "Formation",
            "date" => "Date",
        ],
        'contents_valid' => [
            'nameMatrix' => 'Formation',
            'nameDomain' => 'Domaine',
            'nameSkill' => 'Compétence',
            'nameTheme' => 'Thème',
            'nameAcquis' => 'Acquis',
            'nameContent' => 'Contenu',
            "categoryName" => 'Statut',
        ],

        'rappels_memo' => [
            'groupePrincipal' => 'Groupe Principal',
            'groupe' => 'Groupe',
            'firstName' => 'Prénom',
            'lastName' => 'Nom',
            'email' => 'Adresse email',
            'matrixName' => 'Formation',
            'acquisName' => 'Acquis',
            'current_box' => 'Boite actuelle',
            'memory_retention_rate' => 'Taux de mémorisation',
            'percentage_failed' => 'Taux d\'échec'
        ],

        'total_contents_valid' => [
            'companyName' => 'Groupe Principal',
            'parent1Name' => 'Groupe',
            'firstName' => 'Prénom',
            'lastName' => 'Nom',
            'email' => 'Adresse email',
            'matrixName' => 'Formation',
            'contentCount' => 'Nombre total de contenus validés'
        ],

        'total_contents_failed' => [
            'companyName' => 'Groupe Principal',
            'parent1Name' => 'Groupe',
            'firstName' => 'Prénom',
            'lastName' => 'Nom',
            'email' => 'Adresse email',
            'matrixName' => 'Formation',
            'contentCount' => 'Nombre total de contenus non validés'
        ],

        'total_connections' => [
            'organisation' => 'Groupe Principal',
            'sousOrganisation' => 'Groupe',
            'firstName' => 'Prénom',
            'lastName' => 'Nom',
            'email' => 'Adresse email',
            'date' => 'Date',
            'totalConnexion' => 'Nombre total de connexions'
        ],

        'total_learning_time' => [
            'groupePrincipal' => 'Groupe Principal',
            'groupe' => 'Groupe',
            'firstName' => 'Prénom',
            'lastName' => 'Nom',
            'email' => 'Adresse email',
            'time' => 'Temps passé dans l\'apprentissage'
        ]
    ];

    const legende = [
        'performance' => [
            'Pourcentage de contenus validés : ',
            '* Les éléments avec 0% de contenus ne sont pas affichés'
        ],
        'analytics_time' => [
            'Temps = Temps d\'activité entre chaque réponse entre l\'utilisateur et Tiny',
            "Fin d'une session = Au-delà de 5 minutes d'inaction entre l'utilisateur et Tiny ou 20 minutes pour le temps de lecture d'un contenu",
            "Avant le 15/06/2022 : Données estimées"
        ],
        'connections'   => [],
        'feedback' => [
            "Les valeurs représentent le pourcentage des feedbacks en fonction du nombre total de feedback de la formation."
        ],
        'user_contributions' => [
            'Les valeurs représentent le nombre de contributions à l\'amélioration de la formation sélectionnée'
        ],
        'openaccounts' => [
            "L'appellation 'compte ouvert' est utilisée pour désigner un compte créé par un utilisateurs ayant le rôle d'administrateur client, de super administrateur client ou faisant partie de l'équipe de Tinycoaching."
        ],

        "contents_valid" => [],
        "rappels_memo" => [],
        "total_connections" => [],
        "total_learning_time" => [],
        "total_contents_valid" => [],
        'total_apprenants' => [],
        "total_contents_failed" => []
    ];

    // Modification des profondeurs disponibles au sein de Tinycoaching
    const depthMap = [
        'matrix' => 'Formation',
        'domain' => 'Domaine',
        'skill' => 'Compétence',
        'theme' => 'Thème',
        'acquis' => 'Acquis',
        'content' => 'Contenu'
    ];

    /* Les titres ne doivent pas être supérieur à 32 caractères */
    const namePage = [
        'performance' => 'Performance',
        'total_apprenants' => 'Apprenants actifs',
        'analytics_time' => "Temps d'apprentissage",
        'connections'   => "Nombre de connexions",
        'feedback' => 'Feedback',
        'user_contributions' => "Contribution à l'amélioration",
        'openaccounts' => "Ouverture de comptes",
        "contents_valid" => "Contenus validés",
        "rappels_memo" => "Rappels Mémoriels",
        "total_connections" => "Nombre total de connexions",
        "total_learning_time" => "Tps passé dans l'apprentissage",
        "total_contents_valid" => "Nombre de contenus validés",
        "total_contents_failed" => "Nombre de contenus échoués"
    ];

    const staticData = ['total_apprenants', 'total_connections', 'total_learning_time', 'total_contents_valid', 'total_contents_failed'];

    private $graphicsResolver;
    private $em;

    public function __construct(GraphicsResolver $graphicsResolver, ManagerRegistry $mr)
    {
        $this->graphicsResolver = $graphicsResolver;
        $this->em = $mr->getManager("tinycoaching");
    }

    /**
     * @param graphName String : Récupération du nom du graphique pour récupérer ses données.
     */
    public function getGraphData($args)
    {

        $data = in_array($args['graphName'], self::staticData)
            ? json_decode($this->graphicsResolver->getRawData($args)['data'])->dataExtended
            : json_decode($this->graphicsResolver->getGraphData($args)['data'])->dataExtended;

        $dataConverted = $this->convertKeys($data, self::exportData[$args['graphName']]);

        return $dataConverted;
    }

    public function addExcelPage($spreadsheet, $graphData, $key, $graphName)
    {
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex($key + 1); // +1 car la première page, c'est les filtres utilisés

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(self::namePage[$graphName]);

        // Ajouter la légende du graphique + commencer au début ou non si le graphique possède ou non une légende
        $startLineX = 1;
        if(self::legende[$graphName] !== []){
            $spreadsheet = $this->addLegends($sheet, self::legende[$graphName], self::exportData[$graphName]);
            $startLineX = 5;
        }


        foreach ($graphData as $rowNum => $row) {
            foreach ($row as $colNum => $col) {
                // +1 for column, +5 for row to start from A5
                $sheet->setCellValueByColumnAndRow($colNum + 1, $rowNum + $startLineX, $col);

                // If we are on the first line (header), change the style
                // Change the condition to $rowNum === 0 - 2 to apply the style to the new header row
                if ($rowNum === 0) {
                    $cell = $sheet->getCellByColumnAndRow($colNum + 1, $rowNum + $startLineX);
                    $this->setHeaderCellStyle($cell);
                }

                // Auto size column to fit the column data
                $sheet->getColumnDimensionByColumn($colNum + 1)->setAutoSize(true);
            }
        }

        if ($graphData === []) {
            // If no data, start from A5
            $this->addHeaderWhenEmptyData($graphName, $sheet, $startLineX);
        }

        return $spreadsheet;
    }


    public function addCSVToZip($graphData, $graphName)
    {

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Remplissez le spreadsheet avec les données
        // (cette partie dépend de la structure exacte de vos données)
        $rowNumber = 1;
        foreach ($graphData as $row) {
            $columnNumber = 1;
            foreach ($row as $cellValue) {
                $sheet->setCellValueByColumnAndRow($columnNumber, $rowNumber, $cellValue);
                $columnNumber++;
            }
            $rowNumber++;
        }

        $csvTmpFile = tempnam(sys_get_temp_dir(), 'symfony_');
        $writer = new Csv($spreadsheet);
        $writer->save($csvTmpFile);

        return $csvTmpFile;
    }

    /**
     * En cas de valeur non existante dans le tableau
     * Ajouter la première ligne du header
     */
    private function addHeaderWhenEmptyData($graphName, $sheet, $startLineX)
    {
        $index = 0;
        foreach (self::exportData[$graphName] as $value) {
            $sheet->setCellValueByColumnAndRow($index + 1, $startLineX, $value);

            // Change cell style
            $cell = $sheet->getCellByColumnAndRow($index + 1, $startLineX);
            $this->setHeaderCellStyle($cell);
            $index += 1;
        }
        return $sheet;
    }

    public function addLegends($sheet, $legends, $columns)
    {

        /**
         * S'il n'y a pas de légende, ca ne sert à rien d'en rajouter une
         */
        //  dd($legends);
        if ($legends !== []) {
            $width = count($columns) + 1;

            // Convert the width to an Excel column reference (e.g., 1 -> A, 2 -> B, etc.)
            $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($width);

            // Merge cells from A1 to $lastColumn1
            $sheet->mergeCells("A1:{$lastColumn}1");

            // Set the value for the merged cell
            $sheet->setCellValue('A1', 'Légende');

            // Set the style for the first row
            $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => [
                        'argb' => 'white',
                    ]
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => '9229ff',
                    ]
                ]
            ]);

            // Enable text wrap for the merged cell
            $sheet->getStyle("A1:{$lastColumn}1")->getAlignment()->setWrapText(true);

            // For each legend, merge the cells from A to $lastColumn and set the legend text
            foreach ($legends as $i => $legend) {
                // $i + 1 because row numbers start at 1, not 0
                $rowNum = $i + 2;

                // Merge cells from A$rowNum to $lastColumn$rowNum
                $sheet->mergeCells("A{$rowNum}:{$lastColumn}{$rowNum}");

                // Set the value for the merged cell
                $sheet->setCellValue("A{$rowNum}", $legend);

                // Enable text wrap for the merged cell
                $sheet->getStyle("A{$rowNum}:{$lastColumn}{$rowNum}")->getAlignment()->setWrapText(true);
            }
        }

        return $sheet;
    }



    private function setHeaderCellStyle($cell)
    {
        $cell->getStyle()
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('9229ff'); // Grey color

        // Change text color to white
        $cell->getStyle()
            ->getFont()
            ->getColor()
            ->setARGB(Color::COLOR_WHITE);

        // Make text bold
        $cell->getStyle()
            ->getFont()
            ->setBold(true);

        return $cell;
    }

    /**
     * Ajout de la première page qui contient les filtres utilisateurs pour générer 
     * les données des différents graphiques présents dans l'export.
     */
    public function addFiltersPage($spreadsheet, $filters)
    {

        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(0); // +1 car la première page, c'est les filtres utilisés

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Filtres utilisés");

        // Setup la première page de l'excel
        $this->setupFiltersExport($sheet, $filters);
    }

    /*
     * Return les valeurs du tableau dans le meilleur format pour les différents exports
     */
    public function reforgedGraphDataToExport($graphData)
    {
        $reformedGraphData = [];
        do {
            $row = array_shift($graphData);
            if ($row) {
                if (!$reformedGraphData) {
                    // Première itération, récupérer les clés de la première ligne
                    $rowData = array_keys((array) $row);
                    $reformedGraphData[] = $rowData;
                }

                $rowData = [];
                foreach ($row as $value) {
                    $rowData[] = $value;
                }

                $reformedGraphData[] = $rowData;
            }
        } while ($row);

        return $reformedGraphData;
    }

    /**
     * Modifier les valeur des clés et supprime les valeurs inutiles lors de l'export
     */
    public function convertKeys($data, $keyMap)
    {
        $newData = [];
        foreach ($data as $item) {
            $newItem = new stdClass();
            foreach ($keyMap as $oldKey => $newKey) {
                if (property_exists($item, $oldKey)) {
                    // Vérifie si la clé est 'depth' pour effectuer une traduction supplémentaire (matrix -> Formation)
                    if ($oldKey === 'depth' && isset(self::depthMap[$item->$oldKey])) {
                        $newItem->$newKey = self::depthMap[$item->$oldKey];
                    } else {
                        $newItem->$newKey = $item->$oldKey;
                    }
                }
            }

            $newData[] = $newItem;
        }

        return $newData;
    }

    public function setupTypeExport($type, $spreadsheet)
    {
        switch ($type) {
            case 'EXCEL':
                return new Writer\Xls($spreadsheet);
                break;
            case 'CSV':
                return new Writer\Csv($spreadsheet);
                break;
        }
    }

    /**
     * Met en place les données sous le format de l'EXCEL et du CSV pour l'export. 
     */
    private function setupFiltersExport($sheet, $filters)
    {

        $organisationsNames = $this->getNamesFromIds($filters['filterType']['organisationsFilter'], Company::class);
        $formationsNames = $this->getNamesFromIds($filters['filterType']['matrixFilter'], Matrix::class);
        $user = $filters['filterType']['idUser'] ? $this->em->getRepository(User::class)->find($filters['filterType']['idUser'])->getEmail() : '';
        $dateBegin = substr($filters['filterType']['beginDate'], 0, -9);
        $endDate = substr($filters['filterType']['endDate'], 0, -9);

        $transformedArray = [];
        $transformedArray[] = ['Groupes', 'Formations', 'Utilisateur', 'Date de début', 'Date de fin'];

        $maxLen = max(
            count($filters["filterType"]["matrixFilter"] ?? []),
            count($filters["filterType"]["organisationsFilter"] ?? []),
            1
        );

        for ($i = 0; $i < $maxLen; $i++) {

            $entry = [$organisationsNames[$i] ?? "", $formationsNames[$i] ?? "", '', '', ''];

            if ($i === 0) {
                $entry[2] = $user ?? "";
                $entry[3] = $dateBegin ?? "";
                $entry[4] = $endDate ?? "";
            }

            $transformedArray[] = $entry;
        }

        foreach ($transformedArray as $rowNum => $row) {
            foreach ($row as $colNum => $col) {

                $sheet->setCellValueByColumnAndRow(
                    $colNum + 1,
                    $rowNum + 1,
                    $col
                );

                // Si nous sommes sur la première ligne (l'en-tête), changeons le style
                if ($rowNum === 0) {
                    $cell = $sheet->getCellByColumnAndRow($colNum + 1, $rowNum + 1);
                    $cell->getStyle()
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('9229ff'); // Couleur grise

                    // Changer la couleur du texte en blanc
                    $cell->getStyle()
                        ->getFont()
                        ->getColor()
                        ->setARGB(Color::COLOR_WHITE);

                    // Mettre le texte en gras
                    $cell->getStyle()
                        ->getFont()
                        ->setBold(true);
                }

                // Auto size column to fit the column data
                $sheet->getColumnDimensionByColumn($colNum + 1)
                    ->setAutoSize(true);
            }
        }
    }

    /**
     * Transformer la liste des ID des filtres GROUPES et FORMATIONS pour récupérer les NOMS
     */
    function getNamesFromIds($ids, $className)
    {
        $repository = $this->em->getRepository($className);
        $entities = $repository->findBy(['id' => $ids]);
        $names = [];

        foreach ($entities as $entity) {
            $names[] = $entity->getName();
        }

        return $names;
    }
}
