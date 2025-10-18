<?php


namespace App\Services;


use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseService
{
    protected BaseRepository $repository;

    public function getAll(): Collection
    {
        return $this->repository->getAll();
    }

    public function findById(int $id): ?Model
    {
        return $this->repository->find($id);
    }

    public function create(array $data): Model
    {
        return $this->repository->create($data);
    }
    public function insert(array $data): bool
    {
        return $this->repository->insert($data);
    }

    public function update(int $id, array $data): ?Model
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getListByFilter($params, $relationships = [])
    {
        return $this->repository->getListByFilter($params,$relationships);
    }
    public function getByFilter($params, $relationships = [])
    {
        return $this->repository->getByFilter($params,$relationships);
    }
    public function updateListByFilter($params, $paramUpdate)
    {
        return $this->repository->updateListByFilter($params,$paramUpdate);
    }
    public function deleteByFilter($params)
    {
        return $this->repository->deleteByFilter($params);
    }
}
