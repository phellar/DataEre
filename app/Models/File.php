<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable =[
        'user_id',
        'file_name',
        'status',
        'file_path',
        'original_name',
        'type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
