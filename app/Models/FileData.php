<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileData extends Model
{
    protected $fillable = [
        'file_id',
        'data'
    ];
}
