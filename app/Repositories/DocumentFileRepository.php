<?php


namespace App\Repositories;


use App\Models\DocumentFile;
use App\Repositories\BaseRepository;

class DocumentFileRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new DocumentFile();
    }
}
