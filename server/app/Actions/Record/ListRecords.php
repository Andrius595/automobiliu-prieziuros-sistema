<?php

namespace App\Actions\Record;

use App\Models\Record;

class ListRecords
{
    public function list(array $searchParams = [], int $perPage = 10,  $sortParams = [], $relations = [])
    {
        $query = Record::query();

        if (!empty($relations)) {
            $query->with($relations);
        }

        foreach ($searchParams as $key => $searchParam) {
            $query->where($key, 'like', "%$searchParam%");
        }

        if (isset($sortParams['sortBy'])) {
             $query->orderBy($sortParams['sortBy'], $sortParams['sortDirection'] ?? 'asc');
        }

        return $query->paginate($perPage);
    }
}
