<?php

namespace App\Jobs;

use App\Models\File;
use App\Models\FileData;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;
use Maatwebsite\Excel\Facades\Excel;

class ProcessFileJob implements ShouldQueue
{
    use Queueable;

    public $file;

    public function __construct(File $file)
    {
        $this->file = $file;
    }

    public function handle(): void
    {
        $file = File::find($this->file->id);

        $file->update(['status' => 'processing']);

        $path = storage_path('app/public/' . $file->file_path);
        $extension = $file->type;

        $data = [];

        // EXCEL FILE PROCESSING

        if (in_array($extension, ['xlsx', 'csv'])) {

            $rows = Excel::toArray([], $path);

            // Take first sheet
            $sheet = $rows[0] ?? [];

            // Clean empty rows
            $data = array_filter($sheet, function ($row) {
                return array_filter($row);
            });
        }

        // =====================
        // PDF FILE PROCESSING
        // =====================
        if ($extension === 'pdf') {

            $parser = new Parser();
            $pdf = $parser->parseFile($path);

            $text = $pdf->getText();

            // Convert text into rows (basic version)
            $lines = explode("\n", $text);

            $data = array_filter($lines);
        }

        // =====================
        // SAVE DATA
        // =====================
        FileData::create([
            'file_id' => $file->id,
            'data' => json_encode(array_values($data))
        ]);

        // mark as done
        $file->update(['status' => 'done']);
    }
}
