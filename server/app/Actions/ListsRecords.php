<?php

namespace App\Actions;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

abstract class ListsRecords
{
    public string $model;

    public Builder $query;

    public function __construct()
    {
        $this->query = $this->model::query();
    }

    public function list(array $searchParams = [], int $perPage = 10,  $sortParams = [], $relations = []): LengthAwarePaginator
    {
        if (!empty($relations)) {
            $this->query->with($relations);
        }

        $this->searchQuery($searchParams);
        $this->sortQuery($sortParams);

        return $this->query->paginate($perPage);
    }

    public function searchQuery($searchParams): static
    {
        foreach ($searchParams as $key => $searchParam) {
            $this->query->where($key, 'like', "%$searchParam%");
        }

        return $this;
    }

    public function sortQuery($sortParams): static
    {
        if (isset($sortParams['sortBy'])) {
            $this->query->orderBy($sortParams['sortBy'], $sortParams['sortDirection'] ?? 'asc');
        }

        return $this;
    }

}

