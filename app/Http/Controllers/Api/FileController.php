<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;
use App\Jobs\ProcessFileJob;

class FileController extends Controller
{
    public function upload(Request $request)
    {
        // file Validatation
        $request->validate([
            'file' => 'required|file|mimes:pdf,csv,xlsx|max:10240'
        ]);

        $user = $request->user();

        // Store file
        $path = $request->file('file')->store('uploads', 'public');

        //  Save to DB
        $file = File::create([
            'user_id' => $user->id,
            'file_path' => $path,
            'original_name' => $request->file('file')->getClientOriginalName(),
            'type' => $request->file('file')->getClientOriginalExtension(),
            'status' => 'pending'
        ]);

        //   Dispatch job
        ProcessFileJob::dispatch($file);

        
        return response()->json([
            'message' => 'File uploaded successfully',
            'data' => $file
        ]);
    }
}
