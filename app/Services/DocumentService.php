<?php


namespace App\Services;

use App\Repositories\DocumentRepository;
use App\Services\BaseService;

class DocumentService extends BaseService
{
    public function __construct(DocumentRepository $repository)
    {
        $this->repository = $repository;
    }

}
