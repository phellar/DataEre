<?php

namespace App\Http\Controllers;

use App\Models\FileData;
use App\Services\DashboardBuilderService;

class DashboardController extends Controller
{
    public function show($fileId, DashboardBuilderService $service)
    {
        $fileData = FileData::where('file_id', $fileId)->first();

        $rows = json_decode($fileData->data ?? '[]', true);

        $dashboard = $service->build($rows);

        return response()->json($dashboard);
    }
}
