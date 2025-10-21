<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentFile extends Model
{
    protected $fillable = ['document_id', 'name', 'path', 'type'];
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

}


