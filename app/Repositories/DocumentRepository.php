<?php


namespace App\Repositories;


use App\Models\Document;
use App\Repositories\BaseRepository;

class DocumentRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new Document();
    }
}
