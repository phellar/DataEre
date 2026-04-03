<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\FileData;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function generate($id)
    {
        $file = File::findOrFail($id);

        $data = FileData::where('file_id', $id)->first();

        $rows = json_decode($data->data ?? '[]', true);

        $pdf = Pdf::loadView('reports.file-report', [
            'file' => $file,
            'rows' => $rows
        ]);

        return $pdf->download("report-file-$id.pdf");
    }
}