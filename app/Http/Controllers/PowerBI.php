<?php

namespace App\Http\Controllers;

use App\Services\PowerBIService;
use Illuminate\Http\Request;

class PowerBI extends Controller
{
    public function showTableData()
    {
        $groupId= 'a2903a95-48c0-4232-83c8-2009ac07ab5e';
        $datasetId = '80894107-f5fb-49f2-8e8f-67fa4febad64';
        $tableName = 'YVeicoli';

        $powerBIService = new PowerBIService();
        $tableData = $powerBIService->queryTable($datasetId, $tableName);

        return $tableData;
//        return view('powerbi.table', compact('tableData'));
    }

    public function showReport()
    {
        $reportId = 'your-report-id';
        $groupId = 'your-group-id';

        $powerBIService = new PowerBIService();
        $embedToken = $powerBIService->getReportEmbedToken($reportId, $groupId);
        $embedUrl = "https://app.powerbi.com/reportEmbed?reportId={$reportId}&groupId={$groupId}";

        return view('powerbi.report', compact('embedToken', 'embedUrl'));
    }
}
