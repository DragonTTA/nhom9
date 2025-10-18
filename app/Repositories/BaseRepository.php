<?php

namespace App\Repositories;

use App\Helpers\ConvertTimeHelper;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;
    const FIELD_CONFIG = ['order_by', 'limit','page'];

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    public function insert(array $attributes): bool
    {
        return $this->model::insert($attributes);
    }

    public function update($id, array $attributes)
    {
        $model = $this->find($id);
        if ($model) {
            $model->update($attributes);
            return $model;
        }
        return null;
    }

    public function delete($id)
    {
        $model = $this->find($id);
        return $model ? $model->delete() : false;
    }

    public function getListByFilter($params, $relationships = [])
    {
        $query = $this->model->newQuery();
        foreach ($params as $field => $value) {
            if (!in_array($field, self::FIELD_CONFIG)) {
                if (is_array($value)) {
                    $query = $query->whereIn($field, $value);
                } else {
                    if ($field == 'start_time_created_at') {
                        $query = $query->where('created_at', '>=', ConvertTimeHelper::formatDateTime($value));
                    }
                    if ($field == 'end_time_created_at') {
                        $query = $query->where('created_at', '<=', ConvertTimeHelper::formatDateTime($value));
                    }
                    if ($field == 'start_time_updated_at') {
                        $query = $query->where('updated_at', '>=', ConvertTimeHelper::formatDateTime($value));
                    }
                    if ($field == 'end_time_updated_at') {
                        $query = $query->where('updated_at', '<=', ConvertTimeHelper::formatDateTime($value));
                    } else {
                        $query = $query->where($field, $value);
                    }
                }
            }
            if ($field == 'order_by') {
                foreach ($value as $keyOrderBy => $valueOrderBy) {
                    $query = $query->orderBy($keyOrderBy, $valueOrderBy);
                }
            }
        }
        if (!empty($relationships)) {
            foreach ($relationships as $relationship) {
                $query = $query->with($relationship);
            }
        }
        if (!empty($params['limit'])) {
            $query = $query->paginate($params['limit']);
        } else {
            $query = $query->get();
        }
        return $query;
    }

    public function updateListByFilter($params, $paramUpdate)
    {
        $query = $this->model->newQuery();
        foreach ($params as $field => $value) {
            if (is_array($value) && !in_array($field, self::FIELD_CONFIG)) {
                $query = $query->whereIn($field, $value);
            } else {
                if ($field == 'start_time_created_at') {
                    $query = $query->where('created_at', '>=', ConvertTimeHelper::formatDateTime($value));
                }
                if ($field == 'end_time_created_at') {
                    $query = $query->where('created_at', '<=', ConvertTimeHelper::formatDateTime($value));
                }
                if ($field == 'start_time_updated_at') {
                    $query = $query->where('updated_at', '>=', ConvertTimeHelper::formatDateTime($value));
                }
                if ($field == 'end_time_updated_at') {
                    $query = $query->where('updated_at', '<=', ConvertTimeHelper::formatDateTime($value));
                } else {
                    $query = $query->where($field, $value);
                }
            }
        }
        return $query->update($paramUpdate);
    }

    public function deleteByFilter($params)
    {
        $query = $this->model->newQuery();
        foreach ($params as $field => $value) {
            if (is_array($value) && !in_array($field, self::FIELD_CONFIG)) {
                $query = $query->whereIn($field, $value);
            } else {
                if ($field == 'start_time_created_at') {
                    $query = $query->where('created_at', '>=', ConvertTimeHelper::formatDateTime($value));
                }
                if ($field == 'end_time_created_at') {
                    $query = $query->where('created_at', '<=', ConvertTimeHelper::formatDateTime($value));
                }
                if ($field == 'start_time_updated_at') {
                    $query = $query->where('updated_at', '>=', ConvertTimeHelper::formatDateTime($value));
                }
                if ($field == 'end_time_updated_at') {
                    $query = $query->where('updated_at', '<=', ConvertTimeHelper::formatDateTime($value));
                } else {
                    $query = $query->where($field, $value);
                }
            }
        }
        return $query->delete();
    }

    public function getByFilter($params, $relationships = [])
    {
        $query = $this->model->newQuery();
        foreach ($params as $field => $value) {
            if (!in_array($field, self::FIELD_CONFIG)) {
                if (is_array($value)) {
                    $query = $query->whereIn($field, $value);
                } else {
                    if ($field == 'start_time_created_at') {
                        $query = $query->where('created_at', '>=', ConvertTimeHelper::formatDateTime($value));
                    }
                    if ($field == 'end_time_created_at') {
                        $query = $query->where('created_at', '<=', ConvertTimeHelper::formatDateTime($value));
                    }
                    if ($field == 'start_time_updated_at') {
                        $query = $query->where('updated_at', '>=', ConvertTimeHelper::formatDateTime($value));
                    }
                    if ($field == 'end_time_updated_at') {
                        $query = $query->where('updated_at', '<=', ConvertTimeHelper::formatDateTime($value));
                    } else {
                        $query = $query->where($field, $value);
                    }
                }
            }
            if ($field == 'order_by') {
                foreach ($value as $keyOrderBy => $valueOrderBy) {
                    $query = $query->orderBy($keyOrderBy, $valueOrderBy);
                }
            }
        }
        if (!empty($relationships)) {
            foreach ($relationships as $relationship) {
                $query = $query->with($relationship);
            }
        }
        $query = $query->first();
        return $query;
    }
}
