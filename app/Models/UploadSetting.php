<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'max_file_size_mb',
        'allowed_types',
        'updated_by',
    ];
}


