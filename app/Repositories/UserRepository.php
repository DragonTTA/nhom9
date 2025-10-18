<?php


namespace App\Repositories;


use App\Models\User;
use App\Repositories\BaseRepository;

class UserRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new User();
    }
}
