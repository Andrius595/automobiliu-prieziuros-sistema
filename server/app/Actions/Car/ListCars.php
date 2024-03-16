<?php

namespace App\Actions\Car;

use App\Models\Car;

class ListCars
{
    public function list(array $searchParams = [], int $perPage = 10,  $sortParams = [], $relations = [])
    {
        $query = Car::query();

        if (!empty($relations)) {
            $query->with($relations);
        }

        foreach ($searchParams as $key => $searchParam) {
            match ($key) {
                'owner_id' => $query->where('owner_id', $searchParam),
                'owner_first_name' => $query->whereHas('owner', function ($q) use ($searchParam) {
                    $q->where('first_name', 'like', "%$searchParam%");
                }),
                'service_id' => $query->whereHas('appointments', function ($q) use ($searchParam) {
                    $q->where('completed_at', null)
                        ->where('service_id', $searchParam);
                }),
                default => $query->where($key, 'like', "%$searchParam%"),
            };
        }

        if (isset($sortParams['sortBy'])) {
            match ($sortParams['sortBy']) {
                'owner' => $query->join('users', 'cars.owner_id', '=', 'users.id')
                    ->orderBy('users.first_name', $sortParams['sortDirection']),
                default => $query->orderBy($sortParams['sortBy'], $sortParams['sortDirection'] ?? 'asc'),
            };
        }

        return $query->paginate($perPage);
    }
}
