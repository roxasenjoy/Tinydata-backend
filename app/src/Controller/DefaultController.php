<?php

namespace App\Controller;

use App\Service\ExportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Component\HttpFoundation\StreamedResponse;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class DefaultController extends AbstractController
{

    /**
     * @Route("/default", name="default")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Thanks for using Tinydata <3',
        ]);
    }

    /**
     * @Route("/export", name="export")
     */
    public function export(ExportService $exportService): Response
    {

        $graphs = json_decode($_GET["graphs"], true);
        $type = $_GET['type'];
        $filters = json_decode($_GET['filters'], true);
        $specificGraphData = json_decode($_GET['specificGraphData'], true);
        $nameExport = '';

        $args['filterType'] = $filters;

        // Mise en place du fichier de base
        $spreadsheet = new Spreadsheet();
        $nameExport = date("d-m-y") . "_Tinycoaching";

        if ($type === 'EXCEL') {
            $exportService->addFiltersPage($spreadsheet, $args);

            foreach ($graphs as $key => $graphName) {
                $graphName = strtolower($graphName);
                $reformedGraphData = $this->fetchReformedGraphData($exportService, $graphName, $args, $specificGraphData);
                $exportService->addExcelPage($spreadsheet, $reformedGraphData, $key, $graphName);
            }

            $nameExport .= ".xls";
            $writer = $exportService->setupTypeExport($type, $spreadsheet);
            $response =  new StreamedResponse(function () use ($writer) {
                $writer->save('php://output');
            });

            $response->headers->set('Content-Type', 'application/vnd.ms-excel; charset=utf-8');
            $response->headers->set('Content-Disposition', 'attachment;filename=' . $nameExport);
            $response->headers->set('Cache-Control', 'max-age=0');
            return $response;
            
        } else if ($type === 'CSV') {
            $tmpFile = tempnam(sys_get_temp_dir(), 'symfony_') . '.zip';
            $zip = new \ZipArchive();

            if ($zip->open($tmpFile, \ZipArchive::CREATE) !== true) {
                return new Response('Impossible de créer un fichier ZIP.');
            }

            foreach ($graphs as $key => $graphName) {
                $graphName = strtolower($graphName);
                $reformedGraphData = $this->fetchReformedGraphData($exportService, $graphName, $args, $specificGraphData);
                $csvTmpFile = $exportService->addCSVToZip($reformedGraphData, $graphName);
                $zip->addFile($csvTmpFile, $graphName . '.csv');
            }

            $zip->close();
            $file = new File($tmpFile);
            $nameExport .= ".zip";
            $response = $this->file($file, $nameExport, ResponseHeaderBag::DISPOSITION_ATTACHMENT);
            $response->deleteFileAfterSend(true);
            return $response;
        }
    }

    function fetchReformedGraphData($exportService, $graphName, $args, $specificGraphData) {
        $args = array_merge($args, $this->getGraphDataArguments($graphName, $specificGraphData));
        $graphData = $exportService->getGraphData($args);
        return $exportService->reforgedGraphDataToExport($graphData);
    }

    
    function getGraphDataArguments($graphName, $specificGraphData)
    {
        $matchingValue = [];
        $graphName = strtolower($graphName);
        foreach ($specificGraphData as $data) {
            if ($data["key"] === $graphName) {
                $matchingValue = $data["value"];
                break;
            }
        }

        return [
            'specificGraphData' => $matchingValue,
            'graphName' => $graphName
        ];
    }
}
