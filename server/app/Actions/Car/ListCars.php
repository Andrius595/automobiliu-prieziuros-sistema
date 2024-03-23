<?php

namespace App\Actions\Car;

use App\Actions\ListsRecords;
use App\Models\Car;

class ListCars extends ListsRecords
{
    public string $model = Car::class;

    public function searchQuery($searchParams): static
    {
        foreach ($searchParams as $key => $searchParam) {
            match ($key) {
                'owner_id' => $this->query->where('owner_id', $searchParam),
                'owner_first_name' => $this->query->whereHas('owner', function ($q) use ($searchParam) {
                    $q->where('first_name', 'like', "%$searchParam%");
                }),
                'service_id' => $this->query->whereHas('appointments', function ($q) use ($searchParam) {
                    $q->where('completed_at', null)
                        ->where('service_id', $searchParam);
                }),
                default => $this->query->where($key, 'like', "%$searchParam%"),
            };
        }

        return $this;
    }

    public function sortQuery($sortParams): static
    {
        if (isset($sortParams['sortBy'])) {
            match ($sortParams['sortBy']) {
                'owner' => $this->query->join('users', 'cars.owner_id', '=', 'users.id')
                    ->orderBy('users.first_name', $sortParams['sortDirection']),
                default => $this->query->orderBy($sortParams['sortBy'], $sortParams['sortDirection'] ?? 'asc'),
            };
        }

        return $this;
    }
}
