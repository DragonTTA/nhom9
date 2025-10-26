<?php


namespace App\Services;

use App\Repositories\DocumentFileRepository;
use App\Services\BaseService;

class DocumentFileService extends BaseService
{
    public function __construct(DocumentFileRepository $repository)
    {
        $this->repository = $repository;
    }

}
